<?php

namespace Smalls\Pay\Exception;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:23
 **/
class GatewayException extends Exception
{


    public function __construct(string $message = "")
    {
        parent::__construct('ERROR_GATEWAY: ' . $message, Exception::ERROR_GATEWAY, null);
    }

}