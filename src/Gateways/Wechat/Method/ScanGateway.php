<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Wechat\Method;

use Smalls\Pay\Events;
use Smalls\Pay\Gateways\Wechat\Gateway;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Request;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 0:00
 **/
class ScanGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    public function pay(string $endpoint, array $payload): Collection
    {
        $payload['spbill_create_ip'] = Request::ip();
        $payload['trade_type'] = $this->getTradeType();
        Events::dispatch(new Events\PayStarted('Wechat', 'Scan', $endpoint, $payload));
        return $this->preOrder($payload);
    }

    protected function getTradeType(): string
    {
        return 'NATIVE';
    }
}