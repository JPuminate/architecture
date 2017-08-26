<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 16:31
 */

namespace JPuminate\Architecture\EventBus\Connections;


use PhpAmqpLib\Channel\AMQPChannel;

interface RabbitMQConnectionManager
{
    public function isConnected(): bool;
    public function tryConnect();
    public function createChannel($channel_id = null): AMQPChannel;
    public function dispose();
}