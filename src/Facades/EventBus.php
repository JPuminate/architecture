<?php

namespace JPuminate\Architecture\EventBus\Facades;

use JPuminate\Contracts\EventBus\EventBus as EventBusToken;
use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 12:23
 */

class EventBus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EventBusToken::class;
    }
}