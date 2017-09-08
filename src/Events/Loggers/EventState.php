<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 08/09/2017
 * Time: 12:27
 */

namespace JPuminate\Architecture\EventBus\Events\Loggers;


class EventState
{
    public static $PUBLISHED = 0;
    public static $PUBLISHED_FAILED = 1;
    public static $NOT_PUBLISHED = 2;
}