<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 26/08/2017
 * Time: 03:20
 */

namespace JPuminate\Architecture\EventBus;


use JPuminate\Contracts\EventBus\EventHandler;

class DefaultHandlerMaker implements HandlerMaker
{

    public function make($handler): EventHandler
    {
        return new $handler();
    }
}