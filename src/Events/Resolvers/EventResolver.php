<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 30/08/2017
 * Time: 14:18
 */
namespace JPuminate\Architecture\EventBus\Events\Resolvers;


interface EventResolver
{
    public function resolve($event);
    public function getAllEvents();
    public function getAdapter();
}