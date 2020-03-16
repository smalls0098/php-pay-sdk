<?php

namespace Smalls\Pay\Gateways\Qqpay;

use Smalls\Pay\Interfaces\IGateway;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Log;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 15:45
 **/
abstract class Gateway implements IGateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    public abstract function pay(string $endpoint, array $payload);

    public function find($order): array
    {
        return [
            'endpoint' => 'qpay_order_query.cgi',
            'order' => is_array($order) ? $order : ['out_trade_no' => $order],
            'cert' => false,
        ];
    }

    abstract protected function getTradeType();


    protected function preOrder($payload): Collection
    {
        $payload['sign'] = Support::generateSign($payload);
        //var_dump($payload);die;
        Log::debug('Pre Order:', ["qpay_unified_order.cgi", $payload]);
        return Support::requestApi("qpay_unified_order.cgi", $payload);
    }


}