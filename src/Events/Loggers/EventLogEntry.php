<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 08/09/2017
 * Time: 12:30
 */

namespace JPuminate\Architecture\EventBus\Events\Loggers;


use JPuminate\Contracts\EventBus\Events\Event;

class EventLogEntry
{
    private $eventId;
    private $creationTime;
    private $eventType;
    private $content;
    private $state;
    private $timeSent;

    public function __construct(Event $event)
    {
        $this->eventId = $event->getId();
        $this->creationTime = $event->getCreationDate();
        $this->eventType = $event->getEventName();
        $this->content = Event::deserialize($event);
        $this->state = EventState::$NOT_PUBLISHED;
        $this->timeSent = 0;
    }

    /**
     * @param int $state
     */
    public function setState(int $state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getEventId(): string
    {
        return $this->eventId;
    }

    /**
     * @return false|string
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @return Event
     */
    public function getContent(): Event
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getTimeSent(): int
    {
        return $this->timeSent;
    }

    /**
     * @param int $timeSent
     */
    public function incrementTimeSent()
    {
        $this->timeSent++;
    }

    public function decrementTimeSent()
    {
        $this->timeSent--;
    }


}