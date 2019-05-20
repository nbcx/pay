<?php

/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace platform\weixin;
use nbcx\pay\platform\weixin\Config;

/**
 * Query
 *
 * @package platform\weixin
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/5/20
 */
class Query extends Config {

    public function get() {
        // TODO: Implement get() method.
    }

    public function orderQuery($orderid) {
        $timeOut = 6;
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //应用ID
        $param['appid'] = $this->appid;//self::APPID;
        //商户号
        $param['mch_id'] = $this->mchid;//self::MCHID;
        //随机字符串
        $param['nonce_str'] = $this->nonce_str;

        //微信订单号
        //$param['transaction_id'] = $transaction_id;

        //商户订单号
        $param['out_trade_no'] = $orderid;

        //签名
        $param['sign'] = $this->makeSign($param);

        $xml = $this->toXml($param);
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $response = $this->fromXml($response);
        return $response;
    }

}