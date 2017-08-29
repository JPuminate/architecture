<?php

require_once __DIR__ . '/vendor/autoload.php';

require 'events.php';

use AuronConsultingOSS\Logger\Console;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\DefaultConnectionFactory;
use JPuminate\Architecture\EventBus\Connections\DefaultRabbitMQConnectionManager;
use JPuminate\Architecture\EventBus\DefaultHandlerMaker;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Contracts\EventBus\Events\EntityEvent;
use JPuminate\Contracts\EventBus\Subscriptions\InMemoryEventBusSubscriptionManager;


$connectionManager = new DefaultRabbitMQConnectionManager($factory = new DefaultConnectionFactory(new ConnectionConfiguration()), new Console());
$logger = new Console();
$subscriptionManager = new InMemoryEventBusSubscriptionManager();
$handlerMaker = new DefaultHandlerMaker();





$eventbus = new EventBusRabbitMQ($connectionManager, $logger, $subscriptionManager, $handlerMaker);

$eventbus->publish($event = new \JPuminate\Architecture\EventBus\Events\EventBusWorkerEvent());


sleep(2);

$eventbus->publish(new UserUpdatedEvent(1, User::class, 'user-service'));

sleep(2);

$eventbus->publish(new UserDeletedEvent(1, User::class));

sleep(9);







