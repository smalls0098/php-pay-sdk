<?php
declare (strict_types=1);

namespace Smalls\Pay;

use Smalls\Pay\Exception\InvalidGatewayException;
use Smalls\Pay\Interfaces\IGatewayApplication;
use Smalls\Pay\Listeners\KernelLogSubscriber;
use Smalls\Pay\Supports\Config;
use Smalls\Pay\Supports\Logger;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/14 - 22:03
 **/
class PayManager
{


    protected $config;


    public function __construct(array $config)
    {
        $this->config = new Config($config);
        $this->registerLogService();
        $this->registerEventService();
    }


    public static function __callStatic($method, $params): IGatewayApplication
    {
        $app = new self(...$params);

        return $app->create($method);
    }

    protected function create($method): IGatewayApplication
    {
        $gateway = __NAMESPACE__ . '\\Gateways\\' . $this->convertType($method);
        if (class_exists($gateway)) {
            return self::make($gateway);
        }
        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }

    protected function make($gateway): IGatewayApplication
    {
        $app = new $gateway($this->config);

        if ($app instanceof IGatewayApplication) {
            return $app;
        }

        throw new InvalidGatewayException("Gateway [{$gateway}] Must Be An Instance Of GatewayApplicationInterface");
    }

    protected function registerLogService()
    {
        $config = $this->config->get('log');
        $config['identify'] = 'smalls.pay.supports';

        $logger = new Logger();
        $logger->setConfig($config);

        \Smalls\Pay\Supports\Log::setInstance($logger);
    }

    protected function registerEventService()
    {
        Events::setDispatcher(Events::createDispatcher());

        Events::addSubscriber(new KernelLogSubscriber());
    }


    private function convertType($method)
    {
        switch ($method) {
            case 'wechat':
                return 'WeChat';
            case 'alipay':
                return 'AliPay';
            case 'qqpay':
                return 'QQPay';
        }
    }

}