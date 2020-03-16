<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Wechat;

use Smalls\Pay\Events;
use Smalls\Pay\Interfaces\IGateway;
use Smalls\Pay\Supports\Collection;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 23:38
 **/
abstract class Gateway implements IGateway
{

    private $mode;

    public function __construct()
    {
        $this->mode = Support::getInstance()->mode;
    }

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    abstract public function pay(string $endpoint, array $payload);


    abstract protected function getTradeType(): string;


    public function find($order): array
    {
        return [
            'endpoint' => 'pay/orderquery',
            'order' => is_array($order) ? $order : ['out_trade_no' => $order],
            'cert' => false,
        ];
    }


    protected function preOrder($payload): Collection
    {
        $payload['sign'] = Support::generateSign($payload);
        Events::dispatch(new Events\MethodCalled('Wechat', 'PreOrder', '', $payload));
        return Support::requestApi('pay/unifiedorder', $payload);
    }

}