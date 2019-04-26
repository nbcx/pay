<?php
namespace nbcx\pay\platform;

/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 17/10/16 上午11:56
 */

class Apple extends IPay {

    /**
     * 同一下单接口
     * @param $receipt
     * @return array
     */
    public function unifiedOrder($goods,$type,$ext=null) {
        $receipt = utf8_encode($ext);
        $jsonData = ['receipt-data'=>$receipt];//这里本来是需要base64加密的，我这里没有加密的原因是客户端返回服务器端之前，已经作加密处理
        $jsonData = json_encode($jsonData);

        if($this->qa) {
            $url = 'https://sandbox.itunes.apple.com/verifyReceipt'; //测试验证地址
        }
        else {
            $url = 'https://buy.itunes.apple.com/verifyReceipt';  //正式验证地址
        }

        $response = cPost($url,$jsonData,60);
        $response = json_decode($response,true);
        $response['receipt-data'] = $receipt;
        return $response;

    }

}