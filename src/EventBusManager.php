<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 16:12
 */

namespace JPuminate\Architecture\EventBus;


class EventBusManager
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function deserializationError($callback){
        $this->app['events']->listen(Events\DeserializationErrorEvent::class, $callback);
    }
}