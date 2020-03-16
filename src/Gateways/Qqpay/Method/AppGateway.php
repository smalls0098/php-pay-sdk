<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Qqpay\Method;

use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Qqpay\Gateway;
use Smalls\Pay\Gateways\Qqpay\Support;
use Smalls\Pay\Supports\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 15:44
 **/
class AppGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function pay(string $endpoint, array $payload): Response
    {
        $payload['appid'] = Support::getInstance()->getConfig("appid");
        $payload['trade_type'] = $this->getTradeType();

        $pay_request = [
            'appid' => Support::getInstance()->getConfig("appid"),
            'partnerid' => Support::getInstance()->getConfig("mch_id"),
            'prepayid' => $this->preOrder($payload)->get('prepay_id'),
            'timestamp' => strval(time()),
            'noncestr' => Str::random(),
            'package' => 'Sign=QQPay',
        ];
        $pay_request['sign'] = Support::generateSign($pay_request);

        return JsonResponse::create($pay_request);
    }

    protected function getTradeType()
    {
        return 'APP';
    }
}