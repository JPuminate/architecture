<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 16:44
 */

namespace JPuminate\Architecture\EventBus\Connections;


interface ConnectionFactory
{
    public function createConnection();
    public function getConnectionConfiguration(): ConnectionConfiguration;
}