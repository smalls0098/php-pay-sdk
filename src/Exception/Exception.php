<?php
declare (strict_types=1);

namespace Smalls\Pay\Exception;

use Throwable;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 17:07
 **/
class Exception extends \Exception
{


    const UNKNOWN_ERROR = 551;
    const INVALID_GATEWAY = 552;
    const INVALID_CONFIG = 553;
    const INVALID_ARGUMENT = 554;
    const ERROR_GATEWAY = 555;
    const ERROR_BUSINESS = 556;
    const INVALID_SIGN = 557;


    public function __construct(string $message = "",int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}