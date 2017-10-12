<?php
/**
 * Created by PhpStorm.
 * User: laptop
 * Date: 11/10/17
 * Time: 09:56 م
 */

namespace JPuminate\Architecture\EventBus\Events\Loggers;


use JPuminate\Contracts\EventBus\Events\Event;

class ConsoleEventLogger implements EventLogger
{

    public function saveEvent(Event $event)
    {
        printf("eventLogger: event saved ...");
    }

    public function saveEventAndMarkItAsPublished(Event $event)
    {
        printf("eventLogger: event saved and mark it as published ...");
    }

    public function markEventAsPublished(Event $event)
    {
        printf("eventLogger: mark event as published ...");
    }
}