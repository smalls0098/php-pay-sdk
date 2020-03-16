<?php
declare (strict_types=1);

namespace Smalls\Pay\Events;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 23:36
 **/
class PayStarting extends Event
{
    /**
     * @var array
     */
    public $params;

    public function __construct(string $driver, string $gateway, array $params)
    {
        $this->params = $params;

        parent::__construct($driver, $gateway);
    }
}