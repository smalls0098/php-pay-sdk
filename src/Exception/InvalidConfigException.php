<?php

namespace Smalls\Pay\Exception;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:28
 **/
class InvalidConfigException extends Exception
{

    public function __construct(string $message = "")
    {
        parent::__construct('INVALID_CONFIG: ' . $message, Exception::INVALID_CONFIG, null);
    }

}