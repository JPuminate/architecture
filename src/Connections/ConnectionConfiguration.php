<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 25/08/2017
 * Time: 21:22
 */

namespace JPuminate\Architecture\EventBus\Connections;


class ConnectionConfiguration
{
    public $host;

    public $username;

    public $password;

    public $port;

    public $retryCount = 10;

    public $retryInterval = 2000000;

    /**
     * ConnectionConfiguration constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param int $port
     */
    public function __construct($host="127.0.0.1", $port=5672, $username="guest", $password="guest")
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }


    /**
     * @return string
     */




    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port)
    {
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * @param int $retryCount
     */
    public function setRetryCount(int $retryCount)
    {
        $this->retryCount = $retryCount;
    }

    /**
     * @return int
     */
    public function getRetryInterval(): int
    {
        return $this->retryInterval;
    }

    /**
     * @param int $retryInterval
     */
    public function setRetryInterval(int $retryInterval)
    {
        $this->retryInterval = $retryInterval;
    }



}