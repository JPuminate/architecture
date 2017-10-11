<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 24/08/2017
 * Time: 16:56
 */
namespace JPuminate\Contracts\EventBus\Events;

interface IntegrationEvent
{

    public function getId();

    public function getDateTime();

    public function getSender();


}