<?php

namespace Smalls\Pay\Gateways\Wechat\Method;

use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Wechat\Gateway;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 13:45
 **/
class RefundGateway extends Gateway
{

    public function find($order): array
    {
        return [
            'endpoint' => 'pay/refundquery',
            'order' => is_array($order) ? $order : ['out_trade_no' => $order],
            'cert' => false,
        ];
    }

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function pay(string $endpoint, array $payload)
    {
        throw new InvalidArgumentException('Not Support Refund In Pay');
    }

    protected function getTradeType(): string
    {
        throw new InvalidArgumentException('Not Support Refund In Pay');
    }
}