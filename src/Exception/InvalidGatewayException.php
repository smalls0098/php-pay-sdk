<?php
declare (strict_types=1);

namespace Smalls\Pay\Exception;


/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:00
 **/
class InvalidGatewayException extends Exception
{


    public function __construct(string $message = "")
    {
        parent::__construct('INVALID_GATEWAY: ' . $message, Exception::INVALID_GATEWAY, null);
    }

}