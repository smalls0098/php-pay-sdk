<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Qqpay\Method;

use Smalls\Pay\Gateways\Qqpay\Gateway;
use Smalls\Pay\Supports\Request;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 15:44
 **/
class ScanGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    public function pay(string $endpoint, array $payload)
    {
        $payload['spbill_create_ip'] = Request::ip();
        $payload['trade_type'] = $this->getTradeType();
        return $this->preOrder($payload);
    }

    protected function getTradeType()
    {
        return 'NATIVE';
    }
}