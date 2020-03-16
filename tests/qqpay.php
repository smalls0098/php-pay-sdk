<?php
/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 18:06
 **/

require_once '../vendor/autoload.php';

$config = [
    'appid' => '1108000000', // QQ互联里面的appid
    'mch_id' => '1524000000', //支付商户ID1515840651
    'key' => '00000000b82d3d6c3be9c1b008000000',//QQ钱包里面的API密钥

    'cert_client' => './cert/apiclient_cert.pem', // optional, 退款，红包等情况时需要用到
    'cert_key' => './cert/apiclient_key.pem',// optional, 退款，红包等情况时需要用到

    'log' => [
        // optional
        'file' => './logs/qqpay' . date('H-m-d') . '.log',
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
    'mode' => 'normal',//只有一个后续有的话会补充
    'notify_url' => 'http://baidu.com/notify.php',
    'return_url' => 'http://baidu.com/return.php',
];

// 支付订单信息
$order = [
    'out_trade_no' => "ORDER" . time(),
    'body' => 'QQ支付-测试标题',
    'total_fee' => 101,
];
//查询订单
$find = [
    'out_trade_no' => '1584337908'
];
//返回结果
$result = \Smalls\Pay\PayManager::qqpay($config)->miniapp($order);
//打印支付订单信息和结果
var_dump($order, $result);

