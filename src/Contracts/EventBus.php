<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 24/08/2017
 * Time: 16:48
 */

namespace JPuminate\Contracts\EventBus;

use JPuminate\Contracts\EventBus\Events\Event;
use JPuminate\Contracts\EventBus\Events\IntegrationEvent;

interface EventBus
{
    public function subscribe($event, $handler);

    public function unSubscribe($event, $handler);

    public function publish(Event $event);

    public function dispose();

    public function listen();

}