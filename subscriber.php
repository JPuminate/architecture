<?php

require_once __DIR__ . '/vendor/autoload.php';

require 'events.php';

use AuronConsultingOSS\Logger\Console;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\DefaultConnectionFactory;
use JPuminate\Architecture\EventBus\Connections\DefaultRabbitMQConnectionManager;
use JPuminate\Architecture\EventBus\DefaultHandlerMaker;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Contracts\EventBus\Subscriptions\InMemoryEventBusSubscriptionManager;


$connectionManager = new DefaultRabbitMQConnectionManager($factory = new DefaultConnectionFactory(new ConnectionConfiguration()), new Console());
$logger = new Console();
$subscriptionManager = new InMemoryEventBusSubscriptionManager();
$handlerMaker = new DefaultHandlerMaker();


class UserCreatedEventHandler implements \JPuminate\Contracts\EventBus\EventHandler{

    public function processEvent(\JPuminate\Contracts\EventBus\Events\IntegrationEvent $event)
    {

            var_dump($event);

    }
}

class UserCreatedEventHandler2 implements \JPuminate\Contracts\EventBus\EventHandler{

    public function processEvent(\JPuminate\Contracts\EventBus\Events\IntegrationEvent $event)
    {
        if($event instanceof UserCreatedEvent){
            var_dump($event);
        }
    }
}

$event = new UserCreatedEvent(1, User::class);


$eventbus = new EventBusRabbitMQ($connectionManager, $logger, $subscriptionManager, $handlerMaker);

$eventbus->subscribe(UserCreatedEvent::class, UserCreatedEventHandler::class);
$eventbus->subscribe(UserDeletedEvent::class, UserCreatedEventHandler::class);
$eventbus->subscribe(UserUpdatedEvent::class, UserCreatedEventHandler::class);



$eventbus->listen();

echo "pppp";

sleep(1000);







