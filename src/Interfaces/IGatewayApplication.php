<?php
declare (strict_types=1);

namespace Smalls\Pay\Interfaces;
/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 16:55
 **/
interface IGatewayApplication
{

    public function pay($gateway, $params);


    public function find($order, string $type);


    public function refund(array $order);


    public function cancel($order);


    public function close($order);

    public function verify($content, bool $refund);


    public function success();

}