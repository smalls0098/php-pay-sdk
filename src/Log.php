<?php
declare (strict_types=1);

namespace Smalls\Pay;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:37
 **/
class Log
{

    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([\Smalls\Pay\Supports\Log::class, $method], $args);
    }


    public function __call($method, $args)
    {
        return call_user_func_array([\Smalls\Pay\Supports\Log::class, $method], $args);
    }

}