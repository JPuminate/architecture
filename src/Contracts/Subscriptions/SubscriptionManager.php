<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 24/08/2017
 * Time: 21:03
 */

namespace JPuminate\Contracts\EventBus\Subscriptions;



interface SubscriptionManager
{
    public function isEmpty();
    public function addSubscription($event, $handler);
    public function removeSubscription($event, $handler);
    public function hasSubscriptionsForEvent($event);
    public function clear();
    public function getHandlersForEvent($event);
    public function getEventTypeFromKey($event_key);
    public function getEventKey($event);
    public function getSubscriptionKey();
}