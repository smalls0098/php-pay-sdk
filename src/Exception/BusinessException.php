<?php

namespace Smalls\Pay\Exception;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:26
 **/
class BusinessException extends Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct('ERROR_BUSINESS: ' . $message, Exception::ERROR_BUSINESS, null);
    }
}