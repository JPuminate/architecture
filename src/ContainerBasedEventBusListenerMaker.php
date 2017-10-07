<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 12:18
 */

namespace JPuminate\Architecture\EventBus;


use Illuminate\Container\Container;
use JPuminate\Contracts\EventBus\EventHandler;

class ContainerBasedEventBusListenerMaker implements HandlerMaker
{

    public function make($handler): EventHandler
    {
        return Container::getInstance()->make($handler);
    }
}