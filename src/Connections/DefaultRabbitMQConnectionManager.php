<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 16:39
 */

namespace JPuminate\Architecture\EventBus\Connections;

use Bgy\TransientFaultHandling\ErrorDetectionStrategies\TransientErrorCatchAllStrategy;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\RetryStrategies\FixedInterval;
use Exceptions\Data\TypeException;
use Psr\Log\LoggerInterface;
use PhpAmqpLib\Channel\AMQPChannel;

class DefaultRabbitMQConnectionManager implements RabbitMQConnectionManager
{
    protected $connectionFactory;

    protected $connection;

    protected $is_disposed;

    protected $logger;

    private $transientHandler;


    public function __construct(ConnectionFactory $factory, LoggerInterface $logger)
    {
        $this->connectionFactory = $factory;
        $this->logger = $logger;
        if(!$this->connectionFactory) throw new TypeException(ConnectionFactory::class);
        if(!$this->logger) throw new TypeException(LoggerInterface::class);
        $this->transientHandler = new RetryPolicy(new TransientErrorCatchAllStrategy(), new FixedInterval($factory->getConnectionConfiguration()->getRetryCount(), $factory->getConnectionConfiguration()->getRetryInterval()));

    }


    public function isConnected(): bool
    {
        return !is_null($this->connection) && $this->connection->isConnected() && !$this->is_disposed;
    }

    public function tryConnect()
    {
        $this->logger->info("RabbitMQ Client is trying to connect");
        $key = fopen(__DIR__ . '/eventbus.lock', 'w');
        if (flock($key, LOCK_EX | LOCK_NB)) {
            $this->transientHandler->execute(function () {
                $this->connection = $this->connectionFactory->createConnection();
            });
            if ($this->isConnected()) {
                $this->logger->info(sprintf("RabbitMQ persistent connection acquired a connection %s and is subscribed to failure events", $this->connectionFactory->getConnectionConfiguration()->getHost()));
                return true;
            } else {
                $this->logger->critical("FATAL ERROR: RabbitMQ connections could not be created and opened");
                return false;
            }
        }

        fclose($key);
    }


    public function createChannel($channel_id = null): AMQPChannel
    {
        if (!$this->isConnected())
        {
            throw new \RuntimeException("No RabbitMQ connections are available to perform this action");
        }

        return $this->connection->channel($channel_id);
    }


    public function dispose()
    {
        if ($this->is_disposed) return;
        $this->is_disposed = true;
        try
        {
            if($this->connection) $this->connection->close();
        }
        catch (\RuntimeException $e)
            {
               $this->logger->critical($e);
            }
    }
}