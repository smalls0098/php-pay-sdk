<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways;

use Smalls\Pay\Exception\GatewayException;
use Smalls\Pay\Exception\InvalidArgumentException;
use Smalls\Pay\Exception\InvalidGatewayException;
use Smalls\Pay\Exception\InvalidSignException;
use Smalls\Pay\Gateways\Qqpay\Support;
use Smalls\Pay\Interfaces\IGateway;
use Smalls\Pay\Interfaces\IGatewayApplication;
use Smalls\Pay\Supports\Collection;
use Smalls\Pay\Supports\Config;
use Smalls\Pay\Supports\Log;
use Smalls\Pay\Supports\Request;
use Smalls\Pay\Supports\Str;
use Smalls\Pay\Supports\Xml;
use Symfony\Component\HttpFoundation\Request as requestSymfony;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:14
 **/
class QQPay implements IGatewayApplication
{
    /*
     * 正常模式
     */
    const MODE_NORMAL = 'normal';

    const URL = [
        self::MODE_NORMAL => 'https://qpay.qq.com/cgi-bin/pay/',
    ];

    protected $gateway;

    /**
     * @var array
     */
    private $payload = [];


    /**
     * QQPay constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->gateway = Support::create($config)->getBaseUri();
        $this->payload = [
            'appid' => $config->get('app_id', ''),
            'mch_id' => $config->get('mch_id', ''),
            'nonce_str' => Str::random(),
            'notify_url' => $config->get('notify_url', ''),
            'fee_type' => 'CNY',
            'sign' => '',
            'trade_type' => '',
            'spbill_create_ip' => Request::ip(),
        ];
    }

    public function __call($method, $params)
    {
        return self::pay($method, ...$params);
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


    /**
     * 支付下单
     * @param string $gateway 网关
     * @param array $params 参数
     * @return mixed
     * @throws InvalidGatewayException
     */
    public function pay($gateway, $params)
    {
        $this->payload = array_merge($this->payload, $params);
        $gateway = __NAMESPACE__ . '\\Qqpay\\Method\\' . Str::studly($gateway) . 'Gateway';
        if (class_exists($gateway)) {
            return $this->makePay($gateway);
        }
        throw new InvalidGatewayException("Pay Gateway [{$gateway}] Not Exists");
    }


    /**
     * 查询订单
     * @param $order
     * @param string $type
     * @return Collection
     * @throws GatewayException
     */
    public function find($order, string $type)
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

        return Support::requestApi(
            $config['endpoint'],
            $this->payload,
            $config['cert']
        );
    }

    /**
     * 订单退款
     * @param array $order 订单信息
     * @return Collection
     */
    public function refund(array $order)
    {
        $this->payload = Support::filterPayload($this->payload, $order, true);

        return Support::requestApi(
            'qpay_refund.cgi',
            $this->payload,
            true
        );
    }


    /**
     * 取消订单
     * @param $order
     */
    public function cancel($order)
    {

    }

    /**
     * 关闭订单
     * @param $order
     * @return Collection
     */
    public function close($order)
    {
        unset($this->payload['spbill_create_ip']);
        $this->payload = Support::filterPayload($this->payload, $order);
        return Support::requestApi('qpay_close_order.cgi', $this->payload);
    }

    /**
     * 异步回调校验
     * @param $content
     * @param bool $refund
     * @return Collection
     * @throws InvalidSignException
     * @throws InvalidArgumentException
     */
    public function verify($content, bool $refund)
    {
        $content = $content ?? requestSymfony::createFromGlobals()->getContent();

        $data = Xml::fromXml($content);
        if ($refund) {
            $decrypt_data = Support::decryptRefundContents($data['req_info']);
            $data = array_merge(Xml::fromXml($decrypt_data), $data);
        }

        Log::debug('Resolved The Received QQPAY Request Data', $data);

        if ($refund || Support::generateSign($data) === $data['sign']) {
            return new Collection($data);
        }

        throw new InvalidSignException('QQPAY Sign Verify FAILED');
    }

    /**
     * 返回成功
     * @return Response
     * @throws InvalidArgumentException
     */
    public function success()
    {
        return Response::create(
            Xml::toXml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']),
            200,
            ['Content-Type' => 'application/xml']
        );
    }
}