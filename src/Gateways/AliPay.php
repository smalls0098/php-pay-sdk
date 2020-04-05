<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways;

use Smalls\Pay\Events;
use Smalls\Pay\Exception\GatewayException;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Exception\InvalidGatewayException;
use Smalls\Pay\Exception\InvalidSignException;
use Smalls\Pay\Gateways\Alipay\Support;
use Smalls\Pay\Interfaces\IGateway;
use Smalls\Pay\Interfaces\IGatewayApplication;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Config;
use Smalls\Pay\Supports\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:13
 **/
class AliPay implements IGatewayApplication
{

    const MODE_NORMAL = 'normal';

    const MODE_DEV = 'dev';

    const URL = [
        self::MODE_NORMAL => 'https://openapi.alipay.com/gateway.do?charset=utf-8',
        self::MODE_DEV => 'https://openapi.alipaydev.com/gateway.do?charset=utf-8',
    ];

    private $gateway;
    /**
     * @var array
     */
    private $payload;

    protected $extends;

    public function __construct(Config $config)
    {
        $this->gateway = Support::create($config)->getBaseUri();
        $this->payload = [
            'app_id' => $config->get('app_id'),
            'method' => '',
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'version' => '1.0',
            'return_url' => $config->get('return_url'),
            'notify_url' => $config->get('notify_url'),
            'timestamp' => date('Y-m-d H:i:s'),
            'sign' => '',
            'biz_content' => '',
            'app_auth_token' => $config->get('app_auth_token'),
        ];
        if ($config->get('app_cert_public_key') && $config->get('alipay_root_cert')) {
            $this->payload['app_cert_sn'] = Support::getCertSN($config->get('app_cert_public_key'));
            $this->payload['alipay_root_cert_sn'] = Support::getRootCertSN($config->get('alipay_root_cert'));
        }
    }

    public function __call($method, $params)
    {
        if (isset($this->extends[$method])) {
            return $this->makeExtend($method, ...$params);
        }

        return $this->pay($method, ...$params);
    }

    protected function makePay(string $gateway)
    {
        $app = new $gateway();
        if ($app instanceof IGateway) {
            return $app->pay($this->gateway, array_filter($this->payload, function ($value) {
                return '' !== $value && !is_null($value);
            }));
        }
        throw new InvalidGatewayException("Pay Gateway [{$gateway}] Must Be An Instance Of GatewayInterface");
    }

    protected function makeExtend(string $method, array ...$params): Collection
    {
        $params = count($params) >= 1 ? $params[0] : $params;

        $function = $this->extends[$method];

        $customize = $function($this->payload, $params);

        if (!is_array($customize) && !($customize instanceof Collection)) {
            throw new InvalidArgumentException('Return Type Must Be Array Or Collection');
        }

        Events::dispatch(new Events\MethodCalled(
            'Alipay',
            'extend - ' . $method,
            $this->gateway,
            is_array($customize) ? $customize : $customize->toArray()
        ));

        if (is_array($customize)) {
            $this->payload = $customize;
            $this->payload['sign'] = Support::generateSign($this->payload);

            return Support::requestApi($this->payload);
        }

        return $customize;
    }

    public function extend(string $method, callable $function, bool $now = true): ?Collection
    {
        if (!$now && !method_exists($this, $method)) {
            $this->extends[$method] = $function;

            return null;
        }

        $customize = $function($this->payload);

        if (!is_array($customize) && !($customize instanceof Collection)) {
            throw new InvalidArgumentException('Return Type Must Be Array Or Collection');
        }

        Events::dispatch(new Events\MethodCalled('Alipay', 'extend', $this->gateway, $customize));

        if (is_array($customize)) {
            $this->payload = $customize;
            $this->payload['sign'] = Support::generateSign($this->payload);

            return Support::requestApi($this->payload);
        }

        return $customize;
    }


    public function pay($gateway, $params)
    {
        Events::dispatch(new Events\PayStarting('Alipay', $gateway, $params));

        $this->payload['return_url'] = $params['return_url'] ?? $this->payload['return_url'];
        $this->payload['notify_url'] = $params['notify_url'] ?? $this->payload['notify_url'];

        unset($params['return_url'], $params['notify_url']);

        $this->payload['biz_content'] = json_encode($params);

        $gateway = __NAMESPACE__ . '\\Alipay\\Method\\' . Str::studly($gateway) . 'Gateway';

        if (class_exists($gateway)) {
            return $this->makePay($gateway);
        }

        throw new InvalidGatewayException("Pay Gateway [{$gateway}] not exists");
    }

    public function find($order, string $type)
    {
        $gateway = get_class($this) . '\\Method\\' . Str::studly($type) . 'Gateway';

        if (!class_exists($gateway) || !is_callable([new $gateway(), 'find'])) {
            throw new GatewayException("{$gateway} Done Not Exist Or Done Not Has FIND Method");
        }

        $config = call_user_func([new $gateway(), 'find'], $order);

        $this->payload['method'] = $config['method'];
        $this->payload['biz_content'] = $config['biz_content'];
        $this->payload['sign'] = Support::generateSign($this->payload);

        Events::dispatch(new Events\MethodCalled('Alipay', 'Find', $this->gateway, $this->payload));

        return Support::requestApi($this->payload);
    }

    public function refund(array $order)
    {
        $this->payload['method'] = 'alipay.trade.refund';
        $this->payload['biz_content'] = json_encode($order);
        $this->payload['sign'] = Support::generateSign($this->payload);

        Events::dispatch(new Events\MethodCalled('Alipay', 'Refund', $this->gateway, $this->payload));

        return Support::requestApi($this->payload);
    }

    public function cancel($order)
    {
        $this->payload['method'] = 'alipay.trade.cancel';
        $this->payload['biz_content'] = json_encode(is_array($order) ? $order : ['out_trade_no' => $order]);
        $this->payload['sign'] = Support::generateSign($this->payload);

        Events::dispatch(new Events\MethodCalled('Alipay', 'Cancel', $this->gateway, $this->payload));

        return Support::requestApi($this->payload);
    }

    public function close($order)
    {
        $this->payload['method'] = 'alipay.trade.close';
        $this->payload['biz_content'] = json_encode(is_array($order) ? $order : ['out_trade_no' => $order]);
        $this->payload['sign'] = Support::generateSign($this->payload);

        Events::dispatch(new Events\MethodCalled('Alipay', 'Close', $this->gateway, $this->payload));

        return Support::requestApi($this->payload);
    }

    public function verify($data, bool $refund)
    {
        if (is_null($data)) {
            $request = Request::createFromGlobals();

            $data = $request->request->count() > 0 ? $request->request->all() : $request->query->all();
        }

        if (isset($data['fund_bill_list'])) {
            $data['fund_bill_list'] = htmlspecialchars_decode($data['fund_bill_list']);
        }

        Events::dispatch(new Events\RequestReceived('Alipay', '', $data));

        if (Support::verifySign($data)) {
            return new Collection($data);
        }

        Events::dispatch(new Events\SignFailed('Alipay', '', $data));

        throw new InvalidSignException('Alipay Sign Verify FAILED');
    }

    public function success()
    {
        Events::dispatch(new Events\MethodCalled('Alipay', 'Success', $this->gateway));

        return Response::create('success');
    }
}