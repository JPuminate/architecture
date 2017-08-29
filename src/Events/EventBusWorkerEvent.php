<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 27/08/2017
 * Time: 13:15
 */

namespace JPuminate\Architecture\EventBus\Events;

use Exceptions\Data\TypeException;
use JPuminate\Contracts\EventBus\Events\Event;

class EventBusWorkerEvent extends Event
{
    public static function deserialize($object): Event{
        $event = new static();
        try {
            foreach ($object as $key => $value) {
                $event->{$key} = $value;
            }
            return $event;
        }
        catch(\Exception $e){
            throw new TypeException("cannot deserialize this object");
        }
    }
}