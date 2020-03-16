<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Alipay;

use Smalls\Pay\Interfaces\IGateway;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 14:45
 **/
abstract class Gateway implements IGateway
{


    protected $mode;


    public function __construct()
    {
        $this->mode = Support::getInstance()->mode;
    }

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    public abstract function pay(string $endpoint, array $payload);

}