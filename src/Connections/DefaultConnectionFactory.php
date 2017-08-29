<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 16:46
 */

namespace JPuminate\Architecture\EventBus\Connections;


use PhpAmqpLib\Connection\AMQPStreamConnection;

class DefaultConnectionFactory implements ConnectionFactory
{

    protected $configuration;

    public function __construct(ConnectionConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }


    public function createConnection()
    {
        return new AMQPStreamConnection(
            $this->configuration->getHost(),
            $this->configuration->getPort(),
            $this->configuration->getUsername(),
            $this->configuration->getPassword());
    }

    public function getConnectionConfiguration(): ConnectionConfiguration
    {
        return $this->configuration;
    }


    public function setConnectionConfiguration(ConnectionConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
}