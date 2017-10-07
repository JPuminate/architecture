<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 26/08/2017
 * Time: 03:19
 */

namespace JPuminate\Architecture\EventBus;


interface EventBusListenerMaker
{
    public function make($handler): EventBusListener;
}