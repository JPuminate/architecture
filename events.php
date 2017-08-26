<?php

use JPuminate\Contracts\EventBus\Events\EntityEvent;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 26/08/2017
 * Time: 03:44
 */

class User{

}

class UserCreatedEvent extends EntityEvent{}
class UserUpdatedEvent extends EntityEvent{}
class UserDeletedEvent extends EntityEvent{}