<?php
declare (strict_types=1);

namespace Smalls\Pay\Events;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/3/15 - 22:46
 **/
class ApiRequesting extends Event
{

    /**
     * @var string
     */
    public $endpoint;
    /**
     * @var array
     */
    public $payload;

    public function __construct(string $driver, string $gateway, string $endpoint, array $payload)
    {
        $this->endpoint = $endpoint;
        $this->payload = $payload;

        parent::__construct($driver, $gateway);
    }

}