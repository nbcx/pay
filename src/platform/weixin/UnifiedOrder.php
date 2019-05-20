<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nbcx\pay\platform\weixin;

/**
 * UnifiedOrder
 *
 * @package platform\weixin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019-05-18
 */
class UnifiedOrder extends Config {

    public function get() {
        $param = [];
        // TODO: Implement get() method.
        switch ($this->trade_type) {
            case 'app':
                $this->trade_type='APP';
                break;
            case 'pc':
                $this->trade_type='NATIVE';
                break;
            case 'h5':
                $this->trade_type='MWEB';
                $param['scene_info']= json_encode([
                    'h5_info'=>[
                        'type'=>'Wap',
                        'wap_url'=>'http://www.lookmanhua.com/',
                        'wap_name'=>'撸卡漫画'
                    ]
                ]);
                break;
            case 'inapp':
                //公众号支付
                $this->trade_type='JSAPI';
                break;
        }
        return $this->unifiedOrder($param);
    }



    /**
     *
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    private function unifiedOrder($param) {
        $timeOut = 6;
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //应用ID
        $param['appid'] = $this->appid;//self::APPID;
        //商户号
        $param['mch_id'] = $this->mchid;//self::MCHID;
        //随机字符串
        $param['nonce_str'] = $this->nonce_str;
        //商品描述
        $param['body'] = $this->goods_name;
        //商户订单号
        $param['out_trade_no'] = $this->out_trade_no;
        //总金额，单位分
        $param['total_fee'] = $this->total_amount;
        //终端IP
        $param['spbill_create_ip'] = $this->spbill_create_ip;//'180.173.208.249';
        //通知地址
        $param['notify_url'] = $this->notify_url;//
        //交易类型
        $param['trade_type'] = $this->trade_type;
        //签名
        $param['sign'] = $this->makeSign($param);
        $xml = $this->toXml($param);
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $response = $this->fromXml($response);
        if ($response['return_code'] != 'SUCCESS') {
            $this->pay->errno = $response['return_code'];
            $this->pay->errmsg = $response['return_msg'];
            return false;
        }
        if($response['result_code'] != 'SUCCESS' ) {
            $this->pay->errno = $response['err_code'];
            $this->pay->errmsg = $response['err_code_des'];
            return false;
        }

        return $response;
    }

}