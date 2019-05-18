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

    //2&4
    protected function app($data) {
        $goods['product_code'] = 'QUICK_MSECURITY_PAY';
        $result = $this->sdkExecute($data);
        return $result;
    }

    protected function h5($data,$ext) {
        $this->method='alipay.trade.wap.pay';
        $result = $this->pageExecute($data,$ext);
        return $result;
    }

    protected function pc($data) {
        $this->method='alipay.trade.precreate';
        return $this->execute($data);
    }

}