<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 24/08/2017
 * Time: 16:59
 */
namespace JPuminate\Contracts\EventBus\Events;

use Exceptions\Data\TypeException;
use JsonSerializable;
use ReflectionClass;

abstract class Event implements IntegrationEvent, JsonSerializable
{

    protected $id;

    protected $creation_date;

    protected $sender;

    protected $event_name;
    /**
     * @var null
     */
    protected $pusher_id;

    public function __construct($sender=null, $pusher_id=null)
    {
        $this->id = uniqid();
        $this->creation_date = date("Y/m/d H:i:sP");
        $this->sender = $sender;
        $this->event_name = static::class;
        $this->pusher_id = $pusher_id;
    }

    public function getId()
    {
       return $this->id;
    }

    public function getDateTime()
    {
        return $this->creation_date;
    }

    public function getSender()
    {
        return $this->sender;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $map = [];
        $properties = (new ReflectionClass($this))->getProperties();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $map[$property->getName()] = $property->getValue($this);
        }
        return $map;
    }

    /**
     * @param null $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param string $event_name
     */
    public function setEventName(string $event_name)
    {
        $this->event_name = $event_name;
    }

    /**
     * @param null $pusher_id
     */
    public function setPusherId($pusher_id)
    {
        $this->pusher_id = $pusher_id;
    }

    /**
     * @return false|string
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->event_name;
    }

    /**
     * @return null
     */
    public function getPusherId()
    {
        return $this->pusher_id;
    }



}