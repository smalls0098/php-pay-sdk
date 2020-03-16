<?php
declare (strict_types=1);
namespace Smalls\Pay\Gateways\Alipay\Method;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\InvalidConfigException;
use Smalls\Pay\Gateways\Alipay\Gateway;
use Smalls\Pay\Gateways\Alipay\Support;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 14:58
 **/
class AppGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidConfigException
     */
    public function pay(string $endpoint, array $payload)
    {
        $payload['method'] = 'alipay.trade.app.pay';

        $biz_array = json_decode($payload['biz_content'], true);
        $payload['biz_content'] = json_encode(array_merge($biz_array, ['product_code' => 'QUICK_MSECURITY_PAY']));
        $payload['sign'] = Support::generateSign($payload);

        Events::dispatch(new Events\PayStarted('Alipay', 'App', $endpoint, $payload));

        return Response::create(http_build_query($payload));
    }
}