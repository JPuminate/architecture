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
use JPuminate\Architecture\EventBus\Events\DeserializationErrorEvent;
use JPuminate\Architecture\EventBus\Events\Loggers\EventLogger;
use JPuminate\Architecture\EventBus\Events\Resolvers\EventResolver;
use JPuminate\Architecture\EventBus\Serialization\JSONDeserializer;
use JPuminate\Contracts\EventBus\EventBus;
use JPuminate\Contracts\EventBus\Events\Event;
use JPuminate\Contracts\EventBus\Events\IntegrationEvent;
use JPuminate\Contracts\EventBus\Subscriptions\InMemoryEventBusSubscriptionManager;
use JPuminate\Contracts\EventBus\Subscriptions\SubscriptionManager;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class EventBusRabbitMQ  implements EventBus
{


    public static $NAME_SPACE = 'EventBus';

    public static $EVENT_NAME_DEL;

    private static $PUBLISH_CHANNEL_ID = 2;

    private static $SUBSCRIBE_CHANNEL_ID = 1;

    private $connectionManager;

    private $logger;

    private $subscriptionManager;

    private $handlerMaker;

    private $rabbit_subscribe_channel;

    private $rabbit_publish_channel;

    private $eventResolver;

    private $transientHandler;

    private $subscriber_prefix = "subscriber";

    private $publisher_id_file = "publisher.id";

    private $publisher_id;

    private $deserializer;

    public static $EVENT_LOG_TABLE = 'IntegrationEventLog';

    public static $RUN_MIGRATION = true;

    private $eventLogger;

    private $async = ['queue' => 'default', 'connection' => 'database'];


    public function __construct(RabbitMQConnectionManager $connectionManager, LoggerInterface $logger, SubscriptionManager $subscriptionManager, EventBusListenerMaker $handlerMaker, EventResolver $resolver, EventLogger $eventLogger, $asyncOptions=null)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->subscriptionManager = $subscriptionManager;
        $this->handlerMaker = $handlerMaker;
        $this->transientHandler = new RetryPolicy(new TransientErrorCatchAllStrategy(), new FixedInterval(5, 1000000));
        $this->publisher_id = $this->generatePublisherId();
        $this->eventResolver = $resolver;
        $this->deserializer = new JSONDeserializer();
        $this->eventLogger = $eventLogger;
        if($asyncOptions){
            if(array_key_exists('queue', $asyncOptions)) $this->async['queue'] = $asyncOptions['queue'];
            if(array_key_exists('connection', $asyncOptions)) $this->async['connection'] = $asyncOptions['connection'];
        }
        register_shutdown_function(array($this, 'dispose'));
        static::$EVENT_NAME_DEL = app()->getNamespace().static::$NAME_SPACE.'\Events\\';
        if($this->subscriptionManager instanceof InMemoryEventBusSubscriptionManager)
            $this->subscriptionManager->setBaseNamespace(static::$EVENT_NAME_DEL);
    }



    public function start(){
        if(!$this->rabbit_subscribe_channel) {
            if(!$this->connectionManager->isConnected()) {
                $this->connectionManager->tryConnect();
            }
            $this->rabbit_subscribe_channel = $this->connectionManager->createChannel(static::$SUBSCRIBE_CHANNEL_ID);
        }
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

    public function publish(Event $event, $logIt=true)
    {
        if (!$this->connectionManager->isConnected()) {
            $this->connectionManager->tryConnect();
        }
        $event_ext = $this->getEventExchangeName($event);
        $this->rabbit_publish_channel = $this->connectionManager->createChannel(static::$PUBLISH_CHANNEL_ID);
        $this->rabbit_publish_channel->exchange_declare($event_ext, 'fanout', false, true, false);
        $event->setPusherId($this->publisher_id);
        $event->setEventName($this->getEventName($event));
        $message = json_encode($event);
        $amqp_msg = new AMQPMessage($message, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->transientHandler->execute(function () use ($amqp_msg, $event_ext) {
            $this->rabbit_publish_channel->basic_publish($amqp_msg, $event_ext);
        });
        if ($logIt) $this->eventLogger->saveEventAndMarkItAsPublished($event);
    }

    public function publishAsync(Event $event, $logIt=true)
    {
        return AsyncPublisher::dispatch($event, $logIt, $this->async)
            ->onQueue($this->async['queue'])
            ->onConnection($this->async['connection']);
    }


    public function dispose()
    {
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
        $this->rabbit_subscribe_channel->queue_declare($queue_name, false, true, false, false);
        $this->rabbit_subscribe_channel->queue_bind($queue_name, $ext);
        $callback = function (AMQPMessage $msg) {
            try {
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                $event = json_decode($msg->body);
                $this->processEvent($event);
            }
            catch (\Exception $e){
                $this->exception_handler($e);
            }
        };
        $this->rabbit_subscribe_channel->basic_consume($queue_name, '',false, false, false, false, $callback);
    }

    private function generatePublisherId()
    {
        $path = __DIR__ . '/' . $this->publisher_id_file;
        if (file_exists($path)) {
            $key = file_get_contents($path);
            if (empty($key)) {
                $key = $this->prefixKey($this->subscriptionManager->getSubscriptionKey());
                file_put_contents($path, $key);
            }
        }
        else file_put_contents($path, $key = $this->prefixKey($this->subscriptionManager->getSubscriptionKey()));
        return $key;
    }

    private function getSubscriptionQueueName($event_key){
        return $this->subscriber_prefix.'.'.$this->publisher_id.'.'.$event_key;
    }

    private function getEventExchangeName($event){

        if($event instanceof IntegrationEvent) {
            $name = 'events.'.$this->subscriptionManager->getEventKey($event);
        }
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
                try {
                    $integrationEvent = $this->deserializer->deserialize($this->getEventClassName($event->event_name), $event);
                    if ($integrationEvent->getPusherId() != $this->publisher_id) {
                        $handlerInstance = $this->handlerMaker->make($handler);
                        if (method_exists($handlerInstance, 'filter'))
                            if ($handlerInstance->filter($integrationEvent)) $handlerInstance->processEvent($integrationEvent);
                            else $handlerInstance->processEvent($integrationEvent);
                    }
                }
                catch(\Exception $e){
                    $this->logger->error($e);
                    \Illuminate\Support\Facades\Event::dispatch(new DeserializationErrorEvent($event, $e));
                }
            }
        }
    }

    public function exception_handler($e){

    }

    private function prefixKey($key)
    {
        return  env('APP_NAME', 'APP').'-'.$key;
    }

    public function getEventResolver(){
        return $this->eventResolver;
    }

    private function getEventName($event)
    {
        $array = explode(static::$EVENT_NAME_DEL, get_class($event));
        return end($array);
    }

    public function getEventClassName($event_name)
    {
        return static::$EVENT_NAME_DEL.$event_name;
    }

}