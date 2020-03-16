<?php
/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 18:06
 **/

require_once '../vendor/autoload.php';

$config = [
    'app_id' => '2016101600000000',
    'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtqb/tWB/pVmhjzBdNANaBXPNRARWxgwveZgM0ssdm+Iw+/JzEhYLrGYYoQKEZ83io4YMabOtSQSRgH0rMEh0899Vdta8OEps5D1eycvil0rFfd6SEiNon2yS/zp7StY2W6KnJXRzZZ7VrWlEO5vWNN45N8x5t/V9b/BV1iPVHb8Eh3k8LxeiultqWk6bSXhS8RR3qtOYVazfK9oXwZWvVbExdfePp3Dvl+Gw+0ZPfXhd0ywfWnojQ/fh++F8xE/JxINnFL4CGLhQ63fULWdZuoq3c4bnyMN5y4hmiJX5v2iVN82YtwvC4lOcaWVa1idf4YcP330rimt4mqaQrIKiCwxxxxxx',
    // 加密方式： **RSA2**
    'private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCe8a9Hnt5qiSgCmuyEWpSyYQ0gMyBc7lev/GLx5n707m4v7ApNQ4pgg9nNIWXjuj1TgmnMFZvEbrFRqNgY4gN9eC0i5X8TL0MrP5K4j9fVd9LRBQJQYibyT/QtdZiY4mglyZdndwd7IeyKmUwuIhxeIedBYl8s6fzmlwD5DNm8XcTsF+fTOtzK2TCX10mkp3CDET/7VOc1p6KucLIUobwnRO6L8sprhSZbvRu+PLRniWoI/DR/YQjKYy4TSFo7Y/qpS64r00qxf3xAWWVTCr95ADKOKkWMH1Wnsek8kxW3GE1Xx6/xj8mDYryduJep5jIOP3bhsWchb69jbmT2NEe7AgMBAAECggEAJilS8zgyoJOTtqRpuxFgeD+S/jMoRwe2p/tJ6lh0DOyeKgVbJ9fahnfBuF2XcAjMJpu+ORDLGGuXSUrnr3m8asiK1cESNJH5Z43Y9VUb9hXR2PuB5bjbLvyBXNTKMZLSfJgdMOtEMY0glNpfjwgDlZpAQtOSlF8NqHOC+UjbkjzyT1KV8XF0EAcfhaeiHqeqw4tUww8OerDewVdZGKsi5Ycx8wgEEYoUmyX+btfPAt5KPwMyduBfHmU44Nax52TiS+1/05MiT9xmkWal2kXd3KZs+VbMNEt0iAkrM7tgCRa1OSrGHUEnuh8QfZXmMZ4D6nauhhK0V2LHjvwm3UwHCQKBgQD4UVrJHa8gIqPGWzRPlRxA4SkYwSERzUHyzS4rzyiOZOqISkxF96nBYWsKZMcIIAEn6NE2cS0WFFaveeICekXZbMybz6dQLNCxZtwidIF74AsdPkRXbwNGGrR3PMoZlKSeEy+4VTAgCu+eVaFN759U5gNOyoeI6vIhPsXUo7R9XwKBgQCj3IBqDXWmq1AnLYDlXHxGw6HEPNMm1y/DRQnuo8Ryx5tOjFPulePq0q2r8c2QH0sAfV5UwRadRIf913mHV1Ihzw4VlIekgHDtzLKvnZv87kk4AJ7r3fUEURtkvbzqr6ii/I1vsf3NLW9AodO1Tye/fXotDoElnjj6+TUaEYl3JQKBgFRS1CB9mw2vlavzJsVrnkeN7nXAh6lS6XU5JdesploMfPRBPrt3ycaofT/vTwM8UwgpcHorXqMjzvgRzKUIvpWB881pa6i3Pzsu8cwlgh79yuhoT67dPOBeiy/+jaa4Klqfq1HOY+RNsmczLu6XU6Tx4uersPDWz9hoR6fY257DAoGAFFHgvsYCg/OUkfcrl8W7R480/T/Js3RV4PIrxCMc1lr4YGo4ckq2I8WScdMfebLXuyzQyNPU+RWpg4n38RecAMNLbNOpanXfCy4qLmruEBSAkehJzNgObUWdjwWUasnzsJp+843v+kSbGjm3JjG1rSJjRzkYPND9IepLcnsgP90CgYEA6G1QiyN8yJqvfm4/WjBYbtvURzXWCgyhIxh4BUWdXuQdq2ZLc7Ua3rpyIadW89/8VYn1pwC7eH7MpX4UyeT8Jg/u9qCGlWZmy9puQoJ0Y6uJla/AoxB7uegCYTVC/w8A+j3OQ1eVaHBOyY/3bAhrmKZrHOk8YsRJyEeuAGxxxxxx',
    'log' => [
        // optional
        'file' => './logs/alipay' . date('H-m-d') . '.log',
        'level' => 'DEBUG', // 建议生产环境等级调整为 INFO，开发环境为 DEBUG
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [
        // optional
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
    ],
    // optional,设置此参数，将进入沙箱模式
    'mode' => 'dev',
    //通知地址
    'notify_url' => 'http://baidu.com/notify.php',
    'return_url' => 'http://baidu.com/return.php',
];

// 支付订单信息
$order = [
    'out_trade_no' => "ORDER" . time(),
    'subject' => '支付宝支付-测试标题',
    'total_amount' => '1.01',
    'buyer_id' => 2088622190161234,
];
//查询订单
$find = [
    'out_trade_no' => '1584337908'
];
//返回结果
$result = \Smalls\Pay\PayManager::alipay($config)->miniapp($order);
//打印支付订单信息和结果
var_dump($order, $result);

