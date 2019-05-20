<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nbcx\pay\platform\zhifubao;

/**
 * UnifiedOrder
 *
 * @package platform\zhifubao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/5/13
 */
class UnifiedOrder extends Config {

    public function get() {
        // TODO: Implement get() method.
        $data = [
            'body' => $this->goods_name,//$goods['name']
            'subject' => $this->goods_name,
            'out_trade_no' => $this->out_trade_no,//此订单号为商户唯一订单号
            'total_amount' => $this->total_amount,//保留两位小数
        ];
        switch ($this->trade_type) {
            case 'APP':
                $result = $this->sdkExecute($data);
                break;
            case 'PC':
                $result = $this->pc($data);
                break;
            case 'H5':
                $result = $this->h5($data);
                break;
            default:
                $this->errno = 'FAIL';
                $this->pay->errmsg = 'invalid trade_type';
                return false;
        }
        return $result;
    }

    /**
     * APP支付
     * 生成用于调用收银台SDK的字符串
     * @param $request SDK接口的请求参数对象
     * @return string
     * @author guofa.tgf
     */
    protected function sdkExecute($goods) {
        $goods['product_code'] = 'QUICK_MSECURITY_PAY';

        $params['app_id'] = $this->appid;
        $params['method'] = 'alipay.trade.app.pay';//$request->getApiMethodName();
        $params['format'] = $this->format;
        $params['sign_type'] = $this->signType;
        $params['timestamp'] = date("Y-m-d H:i:s");
        $params['charset'] = $this->postCharset;

        $params['version'] = $this->apiVersion;//$this->checkEmpty($version) ? $this->apiVersion : $version;

        $params["notify_url"] = $this->notify_url;

        $params['biz_content'] = json_encode($goods);//$dict['biz_content'];

        ksort($params);

        $params['sign'] = $this->generateSign($params, $this->signType);
        foreach ($params as &$value) {
            $value = $this->characet($value, $params['charset']);
        }
        return http_build_query($params);
    }

    protected function h5($data,$ext=[]) {
        $this->method='alipay.trade.wap.pay';
        $result = $this->pageExecute($data,$ext);
        return $result;
    }

    protected function pc($data) {
        $this->method='alipay.trade.precreate';
        return $this->execute($data);
    }

    //生成扫码支付预付款订单
    protected function execute($goods, $authToken = null, $appInfoAuthtoken = null) {
        //组装系统参数
        $sysParams["app_id"] = $this->appId;
        $sysParams["method"] = $this->method;//'alipay.trade.precreate';//$request->getApiMethodName();
        $sysParams["format"] = $this->format;
        $sysParams["charset"] = $this->postCharset;
        $sysParams["sign_type"] = $this->signType;
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams["version"] = $this->apiVersion;//$iv;
        $sysParams["notify_url"] = $this->notifyUrl;//$request->getNotifyUrl();

        $sysParams["auth_token"] = $authToken;
        $sysParams["alipay_sdk"] = $this->alipaySdkVersion;
        $sysParams["app_auth_token"] = $appInfoAuthtoken;

        //获取业务参数
        $apiParams['biz_content'] = json_encode($goods);//$request->getApiParas();

        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?";
        //$requestUrl = '';
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

        $resp = cPost($requestUrl, $apiParams);
        $resp = json_decode($resp,true);
        return $resp['alipay_trade_precreate_response'];
    }


    /*
     * 页面提交执行方法
     * @param：跳转类接口的request; $httpmethod 提交方式。两个值可选：post、get
     * @return：构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
     * auther:笙默
    */
    public function pageExecute($goods, $ext) {
        //组装系统参数
        $sysParams["app_id"] = $this->appId;
        $sysParams["version"] = $this->apiVersion;
        $sysParams["format"] = $this->format;
        $sysParams["sign_type"] = $this->signType;
        $sysParams["method"] = $this->method;//$request->getApiMethodName();
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams["alipay_sdk"] = $this->alipaySdkVersion;

        $sysParams["prod_code"] = 'QUICK_WAP_WAY';//$request->getProdCode();
        $sysParams["notify_url"] = $this->notifyUrl;//$request->getNotifyUrl();
        $sysParams["return_url"] = $ext['redirect'];//$request->getReturnUrl();
        $sysParams["charset"] = $this->postCharset;

        //$sysParams["terminal_type"] = $request->getTerminalType();
        //$sysParams["terminal_info"] = $request->getTerminalInfo();

        //获取业务参数
        $apiParams['biz_content'] = json_encode($goods);

        //print_r($apiParams);
        $totalParams = array_merge($apiParams, $sysParams);

        //待签名字符串
        $preSignStr = $this->getSignContent($totalParams);

        //签名
        $totalParams["sign"] = $this->generateSign($totalParams, $this->signType);

        //拼接GET请求串
        $requestUrl = $this->gatewayUrl . "?" . $preSignStr . "&sign=" . urlencode($totalParams["sign"]);
        ///return $this->buildRequestForm($totalParams);
        return $requestUrl;


        if ("GET" == $httpmethod) {

            //拼接GET请求串
            $requestUrl = $this->gatewayUrl . "?" . $preSignStr . "&sign=" . urlencode($totalParams["sign"]);

            return $requestUrl;
        }
        else {
            //拼接表单字符串
            return $this->buildRequestForm($totalParams);
        }
    }

    /**
     * @param $type 支付方式
     * @param $goods  商品信息
     * @return string
     */
    public function unifiedOrder($goods,$type,$ext=null) {
        $data = [
            'body' => $goods['name'],
            'subject' => $goods['name'],
            'out_trade_no' => $goods['orderid'],//此订单号为商户唯一订单号
            'total_amount' => $goods['price']/100,//保留两位小数
        ];
        if($type==2 || $type ==4) {
            $goods['product_code'] = 'QUICK_MSECURITY_PAY';
            $result = $this->sdkExecute($data);
        }
        if($type == 1) {
            $this->method='alipay.trade.precreate';
            $result = $this->execute($data);
        }
        if($type == 3) {
            $this->method='alipay.trade.wap.pay';
            $result = $this->pageExecute($data,$ext);
        }
        return $result;

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            echo "成功";
        }
        else {
            echo "失败";
        }
    }

}