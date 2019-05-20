<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace platform\zhifubao;

use nbcx\pay\platform\zhifubao\Config;

/**
 * Query
 *
 * @package platform\zhifubao
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/5/20
 */
class Query extends Config {

    public function get() {
        // TODO: Implement get() method.
    }

    /**
     * 查询订单
     * @param $orderid 商户订单ID
     */
    public function orderQuery($orderid){
        $params['app_id'] = $this->appId;
        $params['method'] = 'alipay.trade.query';//$request->getApiMethodName();
        $params['format'] = $this->format;
        $params['charset'] = $this->postCharset;
        $params['sign_type'] = $this->signType;
        $params['timestamp'] = date("Y-m-d H:i:s");
        $params['version'] = $this->apiVersion;

        $apiParams['biz_content'] = json_encode(['out_trade_no'=>$orderid]);

        //签名
        $params["sign"] = $this->generateSign(array_merge($params, $apiParams), $this->signType);

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?";

        foreach ($params as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        $resp = cPost($requestUrl, $apiParams);
        $resp = json_decode($resp,true);
        return $resp;
    }

}