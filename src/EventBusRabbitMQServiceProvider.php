<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 27/08/2017
 * Time: 12:54
 */

namespace JPuminate\Architecture\EventBus;


use Illuminate\Support\ServiceProvider;
use JPuminate\Architecture\EventBus\Console\Commands\HandlerMakeCommand;

class EventBusRabbitMQServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->commands([
            HandlerMakeCommand::class
        ]);
    }
}