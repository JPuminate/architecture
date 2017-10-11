<?php
/**
 * Created by PhpStorm.
 * User: laptop
 * Date: 11/10/17
 * Time: 08:19 م
 */

namespace JPuminate\Architecture\EventBus;

use JPuminate\Contracts\EventBus\Events\IntegrationEvent;
use JPuminate\Contracts\EventBus\Subscriptions\SubscriptionManager;

class InMemorySubscriptionManager implements SubscriptionManager
{
    private $handlers;
    private $events;
    private $subscription_key;


    public function __construct()
    {
        $this->handlers = [];
        $this->events = [];
        $this->subscription_key = $this->gen_uuid();
    }
    public function isEmpty()
    {
        return count($this->handlers) == 0;
    }
    public function addSubscription($event, $handler)
    {
        $event_key = $this->getEventKey($event);
        if(!$this->hasSubscriptionsForEvent($event_key)){
            $this->handlers[$event_key] = [];
        }
        if(in_array($handler, $this->handlers[$event_key])){
            throw new \InvalidArgumentException(sprintf("Handler Type %s already registered for '%s'", $handler, $event));
        }
        array_push($this->handlers[$event_key], $handler);
        $this->events[$event_key] = $event;
    }
    public function removeSubscription($event, $handler)
    {
        $event_key = $this->getEventKey($event);
        if (!array_key_exists($event_key, $this->handlers)) return;
        if (($key = array_search($handler, $this->handlers[$event_key])) !== false) {
            unset($this->handlers[$event_key][$key]);
        }
        if (!count($this->handlers[$event_key])) {
            unset($this->handlers[$event_key]);
            unset($this->events[$event_key]);
        }
    }
    public function hasSubscriptionsForEvent($arg, $isKey=true)
    {
        if(!$isKey) $event_key = $this->getEventKey($arg);
        else $event_key = $arg;
        return array_key_exists($event_key, $this->handlers);
    }


    public function clear()
    {
        unset($this->handlers);
        $this->handlers = [];
    }
    public function getHandlersForEvent($arg, $isKey=false)
    {
        if(!$isKey) $event_key = $this->getEventKey($arg);
        else $event_key = $arg;
        if(array_key_exists($event_key, $this->handlers)) return $this->handlers[$event_key];
        else return null;
    }
    public function getEventKey($event)
    {
        if($event instanceof IntegrationEvent) $object = $event;
        else {
            $reflector = new \ReflectionClass($event);
            $object = $reflector->newInstanceWithoutConstructor();
        }
        if(method_exists($object, 'publishedOn') && method_exists($object, 'publishedAs'))
        return $object->publishedOn() . '@' . $object->publishedAs();
        else throw new \InvalidArgumentException("Unsupported event type");
    }
    public function getEventTypeFromKey($event_key)
    {
        return $this->events[$event_key];
    }

    public function getSubscriptionKey()
    {
        return $this->subscription_key;
    }

    private function gen_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            mt_rand( 0, 0x0fff ) | 0x4000
        );
    }
}