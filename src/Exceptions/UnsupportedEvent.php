<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 10:47
 */
namespace JPuminate\Architecture\EventBus\Exceptions;


use Exceptions\Http\Client\UnsupportedMediaTypeException;
use Throwable;

class UnsupportedEvent extends \RuntimeException
{

    protected $message = 'Unsupported Event Type: The eventbus is refusing to handle this event';
    public function __construct()
    {

        parent::__construct($this->message);
    }
}