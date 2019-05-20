# Pay

## 微信下单
```php
$pay = new Pay('weixin','unifiedOrder',[
    'appid'     =>'wxbccf7axxxxxxx',
    'mchid'     => '137015xxxx',
    'key'       => 'MIICdgIBADANBxxxxxxxw0BAQEFAASC',
    'appsecret' => '7813490da6f1265e4901ffbxxxxxxxxx'
]);
$pay->request([
    'body'=>$body,//商品描述
    'out_trade_no' => $out_trade_no,//商户订单号
    'spbill_create_ip'=>$spbill_create_ip,
    'total_fee'=>$total_fee,//总金额，单位分
    'notify_url'=>'http://member.qa.lookmanhua.cosm/notify/weixin',
]);
$result = $pay->get();
```

## 支付宝下单
```php
$pay = new Pay('zhifubao','unifiedOrder',[
    'appid'     =>'wxbccf7axxxxxxx',
    'mchid'     => '137015xxxx',
    'key'       => 'MIICdgIBADANBxxxxxxxw0BAQEFAASC',
    'appsecret' => '7813490da6f1265e4901ffbxxxxxxxxx'
]);
$pay->request([
    'body' => $goods['name'],
    'subject' => $goods['name'],
    'out_trade_no' => $goods['orderid'],//此订单号为商户唯一订单号
    'total_amount' => $goods['price']/100,//保留两位小数        
]);
$result = $pay->get();

$pay->request([
    'goods_name' => $goods['name'], //商品名称
    'out_trade_no' => $goods['name'], //商户订单号
    'spbill_create_ip' => $goods['orderid'],//支付客服端IP
    'total_amount' => $goods['price']/100,//总金额
    'fee_type'=> 'CNY' //币种类型
]);
```