<?php
declare (strict_types=1);

namespace Smalls\Pay\Interfaces;
/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 16:55
 **/
interface IGateway
{

    /**
     * 支付
     * @param string $endpoint 支付的 url
     * @param array $payload 请求支付数据
     * @return mixed
     */
    public function pay(string $endpoint, array $payload);

}