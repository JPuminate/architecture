<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 08/09/2017
 * Time: 12:24
 */
namespace JPuminate\Architecture\EventBus\Events\Loggers;


use JPuminate\Contracts\EventBus\Events\Event;

interface EventLogger
{
    public function saveEvent(Event $event);
    public function saveEventAndMarkItAsPublished(Event $event);
    public function markEventAsPublished(Event $event);
}