<?php
declare (strict_types=1);

namespace Smalls\Pay\Exception;


/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:00
 **/
class InvalidArgumentException extends Exception
{


    public function __construct(string $message = "")
    {
        parent::__construct('INVALID_ARGUMENT: ' . $message, Exception::INVALID_ARGUMENT, null);
    }

}