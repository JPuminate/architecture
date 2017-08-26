<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 16:29
 */

namespace JPuminate\Architecture\EventBus;


use Bgy\TransientFaultHandling\ErrorDetectionStrategies\TransientErrorCatchAllStrategy;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\RetryStrategies\FixedInterval;
use JPuminate\Architecture\EventBus\Connections\RabbitMQConnectionManager;
use JPuminate\Contracts\EventBus\EventBus;
use JPuminate\Contracts\EventBus\Events\IntegrationEvent;
use JPuminate\Contracts\EventBus\Subscriptions\SubscriptionManager;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class EventBusRabbitMQ implements EventBus
{

    private static $PUBLISH_CHANNEL_ID = 2;

    private static $SUBSCRIBE_CHANNEL_ID = 1;

    private $connectionManager;

    private $logger;

    private $subscriptionManager;

    private $handlerMaker;

    private $rabbit_subscribe_channel;

    private $rabbit_publish_channel;

    private $transientHandler;

    private $subscriber_prefix = "subscriber";

    private $subscription_key_file = "subscription.key";

    private $subscription_key = "subscription.key";


    public function __construct(RabbitMQConnectionManager $connectionManager, LoggerInterface $logger, SubscriptionManager $subscriptionManager, HandlerMaker $handlerMaker)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->subscriptionManager = $subscriptionManager;
        $this->handlerMaker = $handlerMaker;
        $this->transientHandler = new RetryPolicy(new TransientErrorCatchAllStrategy(), new FixedInterval(5, 1000000));
        $this->connectionManager->tryConnect();
        $this->rabbit_subscribe_channel = $this->connectionManager->createChannel(static::$SUBSCRIBE_CHANNEL_ID);
        $this->subscription_key = $this->generateSubscriptionKey();
        register_shutdown_function(array($this, 'dispose'));
        set_exception_handler(array($this, 'exception_handler'));
    }

    public function subscribe($event, $handler)
    {
        $event_key = $this->subscriptionManager->getEventKey($event);
        $this->subscriptionManager->addSubscription($event_key, $handler);
        $this->doInternalSubscription($event_key);
    }

    public function unSubscribe($event, $handler)
    {
        $event_key = $this->subscriptionManager->getEventKey($event);
        $this->subscriptionManager->removeSubscription($event_key, $handler);
    }

    public function publish(IntegrationEvent $event)
    {
        if (!$this->connectionManager->isConnected()) {
            $this->connectionManager->tryConnect();
        }
        $event_ext = $this->getEventExchangeName($event);
        $this->rabbit_publish_channel = $this->connectionManager->createChannel(static::$PUBLISH_CHANNEL_ID);
        $this->rabbit_publish_channel->exchange_declare($event_ext, 'fanout', false, true, false);
        $message = json_encode($event);
        $amqp_msg = new AMQPMessage($message);
        $this->transientHandler->execute(function () use($amqp_msg, $event_ext) {
            $this->rabbit_publish_channel->basic_publish($amqp_msg, $event_ext);
        });
    }

    public function dispose()
    {
        echo "------dispose--------\n";
        if($this->rabbit_publish_channel) $this->rabbit_publish_channel->close();
        if($this->rabbit_subscribe_channel) $this->rabbit_subscribe_channel->close();
        $this->connectionManager->dispose();
        $this->subscriptionManager->clear();
    }

    public function __destruct()
    {
       $this->dispose();
    }

    private function doInternalSubscription($event_key)
    {
        if (!$this->connectionManager->isConnected()) {
            $this->connectionManager->tryConnect();
        }
        $this->rabbit_subscribe_channel = $this->connectionManager->createChannel(static::$SUBSCRIBE_CHANNEL_ID);
        $this->rabbit_subscribe_channel->exchange_declare($ext = $this->getEventExchangeName($event_key),'fanout', false, true, false);
        $queue_name = $this->getSubscriptionQueueName($event_key);
        $this->rabbit_subscribe_channel->queue_declare($queue_name, false, true, true, false);
        $this->rabbit_subscribe_channel->queue_bind($queue_name, $ext);
        $callback = function (AMQPMessage $msg) {

            $event = json_decode($msg->body);
            $this->processEvent($event);
        };
        $this->rabbit_subscribe_channel->basic_consume($queue_name, '', false, true, false, false, $callback);
    }

    private function generateSubscriptionKey()
    {
        $path = __DIR__ . '/' . $this->subscription_key_file;
        if (file_exists($path)) {
            $key = file_get_contents($path);
            if (empty($key)) $key = $this->subscriptionManager->getSubscriptionKey();
        } else file_put_contents($path, $key = $this->subscriptionManager->getSubscriptionKey());
        return $key;
    }

    private function getSubscriptionQueueName($event_key){
        return $this->subscriber_prefix.'.'.$this->subscription_key_file.'.'.$event_key;
    }

    private function getEventExchangeName($event){
        if($event instanceof IntegrationEvent) $name = 'events.'.$event->getEventKey();
        else $name = 'events.'.$event;
        return $name;
    }

    public function listen(){
        if($this->rabbit_subscribe_channel){
            while(count($this->rabbit_subscribe_channel->callbacks)) {
                $this->rabbit_subscribe_channel->wait();
            }
        }
    }

    private function processEvent($event)
    {
        $event_key = $this->subscriptionManager->getEventKey($event->event_name);
        if($this->subscriptionManager->hasSubscriptionsForEvent($event_key)){
            $handlers = $this->subscriptionManager->getHandlersForEvent($event_key);
            foreach ($handlers as $handler){
                $reflectedClass = new \ReflectionClass($event->event_name);
                $integrationEvent = $reflectedClass->getMethod('deserialize')->invoke(null, $event);
                $this->handlerMaker->make($handler)->processEvent($integrationEvent);
            }
        }
    }

    public function exception_handler(\Exception $e){

    }

}