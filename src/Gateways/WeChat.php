<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\GatewayException;
use Smalls\Pay\Exception\InvalidGatewayException;
use Smalls\Pay\Exception\InvalidSignException;
use Smalls\Pay\Gateways\Wechat\Support;
use Smalls\Pay\Interfaces\IGateway;
use Smalls\Pay\Interfaces\IGatewayApplication;
use Smalls\Pay\Log;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Config;
use Smalls\Pay\Supports\Request;
use Smalls\Pay\Supports\Str;
use Smalls\Pay\Supports\Xml;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:14
 **/
class WeChat implements IGatewayApplication
{

    /*
     * 正常模式
     */
    const MODE_NORMAL = 'normal';
    /*
     * 沙箱模式
     */
    const MODE_DEV = 'dev';

    const URL = [
        self::MODE_NORMAL => 'https://api.mch.weixin.qq.com/',
        self::MODE_DEV => 'https://api.mch.weixin.qq.com/sandboxnew/',
    ];

    protected $gateway;

    /**
     * @var array
     */
    private $payload = [];

    public function __construct(Config $config)
    {
        $this->gateway = Support::create($config)->getBaseUri();
        $this->payload = [
            'appid' => $config->get('app_id', ''),
            'mch_id' => $config->get('mch_id', ''),
            'nonce_str' => Str::random(),
            'notify_url' => $config->get('notify_url', ''),
            'sign' => '',
            'trade_type' => '',
            'spbill_create_ip' => Request::ip(),
        ];
    }


    public function __call($method, $params)
    {
        return self::pay($method, ...$params);
    }


    public function pay($gateway, $params = [])
    {
        $this->payload = array_merge($this->payload, $params);
        $gateway = __NAMESPACE__ . '\\Wechat\\Method\\' . Str::studly($gateway) . 'Gateway';
        if (class_exists($gateway)) {
            return $this->makePay($gateway);
        }
        throw new InvalidGatewayException("Pay Gateway [{$gateway}] Not Exists");
    }

    protected function makePay($gateway)
    {
        $app = new $gateway();

        if ($app instanceof IGateway) {
            return $app->pay($this->gateway, array_filter($this->payload, function ($value) {
                return '' !== $value && !is_null($value);
            }));
        }

        throw new InvalidGatewayException("Pay Gateway [{$gateway}] Must Be An Instance Of GatewayInterface");
    }

    public function find($order, string $type = 'wap'): Collection
    {
        if ('wap' != $type) {
            unset($this->payload['spbill_create_ip']);
        }

        $gateway = get_class($this) . '\\Method\\' . Str::studly($type) . 'Gateway';

        if (!class_exists($gateway) || !is_callable([new $gateway(), 'find'])) {
            throw new GatewayException("{$gateway} Done Not Exist Or Done Not Has FIND Method");
        }

        $config = call_user_func([new $gateway(), 'find'], $order);
        $this->payload = Support::filterPayload($this->payload, $config['order']);
        Events::dispatch(new Events\MethodCalled('Wechat', 'Find', $this->gateway, $this->payload));

        return Support::requestApi(
            $config['endpoint'],
            $this->payload,
            $config['cert']
        );
    }

    public function refund(array $order)
    {
        $this->payload = Support::filterPayload($this->payload, $order, true);

        Events::dispatch(new Events\MethodCalled('Wechat', 'Refund', $this->gateway, $this->payload));

        return Support::requestApi(
            'secapi/pay/refund',
            $this->payload,
            true
        );
    }

    public function cancel($order)
    {
        unset($this->payload['spbill_create_ip']);

        $this->payload = Support::filterPayload($this->payload, $order, true);

        Events::dispatch(new Events\MethodCalled('Wechat', 'Cancel', $this->gateway, $this->payload));

        return Support::requestApi(
            'secapi/pay/reverse',
            $this->payload,
            true
        );
    }

    public function close($order)
    {
        unset($this->payload['spbill_create_ip']);

        $this->payload = Support::filterPayload($this->payload, $order);

        Events::dispatch(new Events\MethodCalled('Wechat', 'Close', $this->gateway, $this->payload));

        return Support::requestApi('pay/closeorder', $this->payload);
    }

    public function verify($content, bool $refund)
    {
        $content = $content ?? \Symfony\Component\HttpFoundation\Request::createFromGlobals()->getContent();

        Events::dispatch(new Events\RequestReceived('Wechat', '', [$content]));

        $data = Xml::fromXml($content);
        if ($refund) {
            $decrypt_data = Support::decryptRefundContents($data['req_info']);
            $data = array_merge(Xml::fromXml($decrypt_data), $data);
        }

        Log::debug('Resolved The Received Wechat Request Data', $data);

        if ($refund || Support::generateSign($data) === $data['sign']) {
            return new Collection($data);
        }

        Events::dispatch(new Events\SignFailed('Wechat', '', $data));

        throw new InvalidSignException('Wechat Sign Verify FAILED');
    }

    public function success()
    {
        Events::dispatch(new Events\MethodCalled('Wechat', 'Success', $this->gateway));

        return Response::create(
            Xml::toXml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']),
            200,
            ['Content-Type' => 'application/xml']
        );
    }
}