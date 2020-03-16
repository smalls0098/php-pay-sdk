<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Wechat\Method;
use Smalls\Pay\Events;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Gateways\Wechat\Gateway;
use Smalls\Pay\Gateways\Wechat\Support;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 23:32
 **/
class WapGateway extends Gateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function pay(string $endpoint, array $payload): RedirectResponse
    {

        $payload['trade_type'] = $this->getTradeType();
        Events::dispatch(new Events\PayStarted('Wechat', 'Wap', $endpoint, $payload));

        $mWeb_url = $this->preOrder($payload)->get('mweb_url');

        $url = is_null(Support::getInstance()->return_url) ? $mWeb_url : $mWeb_url.
            '&redirect_url='.urlencode(Support::getInstance()->return_url);

        return RedirectResponse::create($url);

    }


    protected function getTradeType(): string
    {
        return 'MWEB';
    }


}