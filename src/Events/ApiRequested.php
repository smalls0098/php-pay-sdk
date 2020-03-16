<?php
declare (strict_types=1);

namespace Smalls\Pay\Events;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:49
 **/
class ApiRequested extends Event
{

    /**
     * @var string
     */
    public $endpoint;
    /**
     * @var array
     */
    public $result;

    public function __construct(string $driver, string $gateway, string $endpoint, array $result)
    {
        $this->endpoint = $endpoint;
        $this->result = $result;

        parent::__construct($driver, $gateway);
    }

}