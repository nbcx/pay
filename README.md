# Pay

## 下单
|参数|类型|说明|
|:-----  |:-----|-----  |
|$platform    |sting    |平台，可填写值：'weixin','zhifubao'|
|$config      |sting    |平台设置|


```php
$pay = new Pay($platform,'unifiedOrder',$config);
$pay->config([
    'trade_type'=>'APP', //支付类型APP | PC | H5 | INAPP
    'goods_name' => $goods['name'], //商品名称
    'out_trade_no' => $goods['name'], //商户订单号
    'spbill_create_ip' => $goods['orderid'],//支付客服端IP
    'total_amount' => $goods['price']/100,//总金额
    'fee_type'=> 'CNY' //币种类型
]);
$result = $pay->get();

//微信$config值：
[
    'appid'     =>'wxbccf7a23f2b304bc',
    'mchid'     => '1370158702',
    'key'       => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASC',
    'appsecret' => '7813490da6f1265e4901ffb80afaa36f',
    'notify_url'=>'http://member.qa.lookmanhua.cosm/notify/weixin',
]

//支付宝$config值：
[
    'appid'               =>'20160721xxxxxxxx',
    'rsaPrivateKey'       => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwxxxxxxxxx',
    'alipayPublicKey'     => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQxxxxxxxxxxxxxx',
    'alipayrsaPublicKey'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBxxxxxxxxxxxxx'
]
```