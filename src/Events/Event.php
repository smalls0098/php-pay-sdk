<?php
declare (strict_types=1);

namespace Smalls\Pay\Events;

use Symfony\Contracts\EventDispatcher\Event as SymfonyEvent;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:47
 **/
class Event extends SymfonyEvent
{

    public $driver;

    public $gateway;

    public $attributes;

    public function __construct(string $driver, string $gateway)
    {
        $this->driver = $driver;
        $this->gateway = $gateway;
    }

}