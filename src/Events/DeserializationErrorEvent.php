<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 15:55
 */

namespace JPuminate\Architecture\EventBus\Events;


class DeserializationErrorEvent
{

    public $payload;
    public $exception;

    public function __construct($payload, $exception)
    {
        $this->payload = $payload;
        $this->exception = $exception;
    }
}