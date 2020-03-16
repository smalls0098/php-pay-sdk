<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Wechat\Method;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Wechat\Gateway;
use Smalls\Pay\Gateways\Wechat\Support;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Str;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 13:34
 **/
class MpGateway extends Gateway
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
        $payload['trade_type'] = $this->getTradeType();

        $pay_request = [
            'appId' => $payload['appid'],
            'timeStamp' => strval(time()),
            'nonceStr' => Str::random(),
            'package' => 'prepay_id=' . $this->preOrder($payload)->get('prepay_id'),
            'signType' => 'MD5',
        ];
        $pay_request['paySign'] = Support::generateSign($pay_request);

        Events::dispatch(new Events\PayStarted('Wechat', 'JSAPI', $endpoint, $pay_request));

        return new Collection($pay_request);
    }


    protected function getTradeType(): string
    {
        return 'JSAPI';
    }
}