<?php
declare (strict_types=1);

namespace Smalls\Pay\Gateways\Alipay\Method;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/16 - 14:48
 **/
class WapGateway extends WebGateway
{

    protected function getMethod(): string
    {
        return 'alipay.trade.wap.pay';
    }

    protected function getProductCode(): string
    {
        return 'QUICK_WAP_WAY';
    }

}