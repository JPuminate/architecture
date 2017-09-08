<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 08/09/2017
 * Time: 13:34
 */

namespace JPuminate\Architecture\EventBus\Events\Loggers;


use Illuminate\Support\Facades\DB;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Contracts\EventBus\Events\Event;

class DBEventLogger implements EventLogger
{

    public function saveEvent(Event $event)
    {
        $entry = new EventLogEntry($event);
        return DB::table(EventBusRabbitMQ::$EVENT_LOG_TABLE)->insert([
            'event_id' => $entry->getEventId(),
            'creation_time' => $entry->getCreationTime(),
            'event_type' => $entry->getEventType(),
            'event_state' => $entry->getState(),
            'event_payload' => $entry->getContent(),
            'time_sent' => $entry->getTimeSent()
        ]);
    }

    public function saveEventAndMarkItAsPublished(Event $event){
        $entry = new EventLogEntry($event);
        $entry->setState(EventState::$PUBLISHED);
        $entry->incrementTimeSent();
        return DB::table(EventBusRabbitMQ::$EVENT_LOG_TABLE)->insert([
            'event_id' => $entry->getEventId(),
            'creation_time' => $entry->getCreationTime(),
            'event_type' => $entry->getEventType(),
            'event_state' => $entry->getState(),
            'event_payload' => $entry->getContent(),
            'time_sent' => $entry->getTimeSent()
        ]);
    }

    public function markEventAsPublished(Event $event)
    {
        $entry = DB::table(EventBusRabbitMQ::$EVENT_LOG_TABLE)->where('event_id', $event->getId())->first();
        $entry->time_sent++;
        $entry->event_state = EventState::$PUBLISHED;
        return $entry->save();
    }
}