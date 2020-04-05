<?php

namespace Smalls\Pay\Gateways\Wechat\Method;

use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Wechat\Support;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 13:37
 **/
class MiniappGateway extends MpGateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function pay(string $endpoint, array $payload)
    {
        $payload['appid'] = Support::getInstance()->miniapp_id;
        return parent::pay($endpoint, $payload);
    }
    
}