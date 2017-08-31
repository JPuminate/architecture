<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';


use App\EventBus\Events\PingEvent;
use AuronConsultingOSS\Logger\Console;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\DefaultConnectionFactory;
use JPuminate\Architecture\EventBus\Connections\DefaultRabbitMQConnectionManager;
use JPuminate\Architecture\EventBus\DefaultHandlerMaker;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Architecture\EventBus\Events\Resolvers\GithubEventResolver;
use JPuminate\Contracts\EventBus\Subscriptions\InMemoryEventBusSubscriptionManager;


$connectionManager = new DefaultRabbitMQConnectionManager($factory = new DefaultConnectionFactory(new ConnectionConfiguration()), new Console());
$logger = new Console();
$subscriptionManager = new InMemoryEventBusSubscriptionManager();
$handlerMaker = new DefaultHandlerMaker();
$resolver = new GithubEventResolver(null, null, null, null);





$eventbus = new EventBusRabbitMQ($connectionManager, $logger, $subscriptionManager, $handlerMaker, $resolver, "Users");

$eventbus->publish(new PingEvent());


sleep(9);







