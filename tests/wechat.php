<?php
/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 18:06
 **/

require_once '../vendor/autoload.php';

$config = [
    'appid' => 'wxXXXXXXXXXXXXX', // APP APPID
    'app_id' => 'wxXXXXXXXXXXXXX', // 公众号 APPID
    'miniapp_id' => 'wxXXXXXXXXXXXXX', // 小程序 APPID
    'mch_id' => '15xxxxxxxx',
    'key' => '',//秘钥
    'cert_client' => './Certs/apiclient_cert.pem', // optional，退款等情况时用到
    'cert_key' => './Certs/apiclient_key.pem',// optional，退款等情况时用到
    'log' => [ // optional
        'file' => './Logs/wechat.log',
        'level' => 'INFO', // 建议生产环境等级调整为 INFO，开发环境为 DEBUG
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [
        // optional
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
    ],
    'mode' => 'dev', // optional, dev/normal;当为 `normal` 时，为正常 gateway。
    //这边配置相关的文件信息
    'notify_url' => 'http://baidu.com/notify.php',//异步回调地址
];

// 支付订单信息
$order = [
    'out_trade_no' => "ORDER" . time(),
    'body' => '微信支付-测试标题',
    'total_fee' => 101,
];
//查询订单
$find = [
    'out_trade_no' => '1584337908'
];
//返回结果
$result = \Smalls\Pay\PayManager::wechat($config)->wap($find);
//打印支付订单信息和结果
var_dump($order, $result);

