<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Alipay\Method;

use Smalls\Pay\Events;
use Smalls\Pay\Gateways\Alipay\Gateway;
use Smalls\Pay\Gateways\Alipay\Support;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 14:49
 **/
class WebGateway extends Gateway
{

    public function pay(string $endpoint, array $payload): Response
    {
        $biz_array = json_decode($payload['biz_content'], true);
        $biz_array['product_code'] = $this->getProductCode();
        $method = $biz_array['http_method'] ?? 'POST';
        unset($biz_array['http_method']);
        $payload['method'] = $this->getMethod();
        $payload['biz_content'] = json_encode($biz_array);
        $payload['sign'] = Support::generateSign($payload);

        Events::dispatch(new Events\PayStarted('Alipay', 'Web/Wap', $endpoint, $payload));

        return $this->buildPayHtml($endpoint, $payload, $method);
    }

    public function find($order): array
    {
        return [
            'method' => 'alipay.trade.query',
            'biz_content' => json_encode(is_array($order) ? $order : ['out_trade_no' => $order]),
        ];
    }


    protected function buildPayHtml($endpoint, $payload, $method = 'POST'): Response
    {
        if ('GET' === strtoupper($method)) {
            return RedirectResponse::create($endpoint . '&' . http_build_query($payload));
        }

        $sHtml = "<form id='alipay_submit' name='alipay_submit' action='" . $endpoint . "' method='" . $method . "'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['alipay_submit'].submit();</script>";

        return Response::create($sHtml);
    }

    protected function getMethod(): string
    {
        return 'alipay.trade.page.pay';
    }

    protected function getProductCode(): string
    {
        return 'FAST_INSTANT_TRADE_PAY';
    }


}