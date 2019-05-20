<?php
namespace nbcx\pay\platform\weixin;

use nbcx\pay\platform\Base;

/**
 *
 * User: Collin
 * QQ: 1169986
 * Date: 17/10/16 下午3:31
 */
abstract class Config extends Base {

    //APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
    public $appid;

    //商户号（必须配置，开户邮件中可查看）
    public $mchid;

    //KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
    public $key;

    //公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
    public $appsecret;


    //随机字符串
    public $nonce_str = '123123123122313';

    public $trade_type = 'app';

    //private $config;

    //public function __construct($config) {
    //    $this->config = $config;

    //    //TODO
    //    $this->appid = $config['appid'];
    //    $this->mchid = $config['mchid'];
    //    $this->key = $config['key'];
    //    $this->appsecret = $config['appsecret'];
    //}

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

    public function notify($xml){
        $xml= $this->fromXml($xml);
        if(!isset($xml['sign'])) {
            return false;
        }
        $sign = $xml['sign'];
        unset($xml['sign']);
        $makeSign = $this->makeSign($xml);
        if($sign != $makeSign) {
            return false;
        }
        return $xml;
    }


    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($values) {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    private function toUrlParams($values) {
        $buff = "";
        foreach ($values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function toXml($values) {
        if (!is_array($values) || count($values) <= 0) {
            throw new Exception("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
            else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function fromXml($xml) {
        if (!$xml) {
            throw new Exception("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @throws WxPayException
     */
    protected function postXmlCurl($xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        //if (self::CURL_PROXY_HOST != "0.0.0.0"
        //    && self::CURL_PROXY_PORT != 0) {
        //    curl_setopt($ch, CURLOPT_PROXY, self::CURL_PROXY_HOST);
        //    curl_setopt($ch, CURLOPT_PROXYPORT, self::CURL_PROXY_PORT);
        //}
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
        //    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        //    curl_setopt($ch, CURLOPT_SSLCERT, self::SSLCERT_PATH);
        //    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        //    curl_setopt($ch, CURLOPT_SSLKEY, self::SSLKEY_PATH);
        //}
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        }
        $error = curl_errno($ch);
        curl_close($ch);
        throw new Exception("curl出错，错误码:$error");
    }

}