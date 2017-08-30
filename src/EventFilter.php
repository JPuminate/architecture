<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 30/08/2017
 * Time: 11:00
 */

namespace JPuminate\Architecture\EventBus;


trait EventFilter
{
    public function filter(){
        return true;
    }

    public function processEvent(){
        return true;
    }
}