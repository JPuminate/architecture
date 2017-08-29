<?php


namespace JPuminate\Architecture\EventBus\Facades;

use JPuminate\Architecture\EventBus\EventBusManager as EventBusManagerToken;
use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 12:23
 */

class EventBusManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EventBusManagerToken::class;
    }
}