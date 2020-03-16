<?php
declare (strict_types=1);

namespace Smalls\Pay\Events;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 23:21
 **/
class SignFailed extends Event
{
    /**
     * @var array
     */
    public $data;

    public function __construct(string $driver, string $gateway, array $data)
    {
        $this->data = $data;
        parent::__construct($driver, $gateway);
    }
}