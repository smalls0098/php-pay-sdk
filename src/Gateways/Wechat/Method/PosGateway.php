<?php

namespace Smalls\Pay\Gateways\Wechat\Method;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Wechat\Gateway;
use Smalls\Pay\Gateways\Wechat\Support;
use Smalls\Pay\Supports\Collection;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 13:40
 **/
class PosGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function pay(string $endpoint, array $payload): Collection
    {
        unset($payload['trade_type'], $payload['notify_url']);

        $payload['sign'] = Support::generateSign($payload);

        Events::dispatch(new Events\PayStarted('Wechat', 'Pos', $endpoint, $payload));

        return Support::requestApi('pay/micropay', $payload);
    }

    protected function getTradeType(): string
    {
        return 'MICROPAY';
    }
}