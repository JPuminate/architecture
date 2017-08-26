<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 26/08/2017
 * Time: 03:19
 */

namespace JPuminate\Architecture\EventBus;


use JPuminate\Contracts\EventBus\EventHandler;

interface HandlerMaker
{
    public function make($handler): EventHandler;
}