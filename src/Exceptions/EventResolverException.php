<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 30/08/2017
 * Time: 14:27
 */

namespace JPuminate\Architecture\EventBus\Exceptions;


use Throwable;

class EventResolverException extends \RuntimeException
{
    /**
     * @var string
     */
    private $event;
    /**
     * @var int
     */
    private $configuration;


    public function __construct($event, $configuration)
    {
        parent::__construct("cannot resolve this event : ".$this->event);
        $this->event = $event;
        $this->configuration = $configuration;
    }
}