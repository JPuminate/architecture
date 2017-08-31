<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use JPuminate\Contracts\EventBus\EventBus;
use Symfony\Component\Console\Input\InputOption;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class EventHostCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'eventbus:host';

    protected $description = 'Upload event class from event resolver';

    protected $type = 'EventBus Event';


    protected function getStub(){
        return __DIR__.'\Stubs\event.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\'.EventBusRabbitMQ::$NAME_SPACE.'\Events';
    }

    protected function buildClass($name)
    {
        $eventBus = Container::getInstance()->make(EventBus::class);
        $content = $eventBus->getEventResolver()->resolve($this->getEventName($name));
        $content = $this->clean($content);
        $replace = ['EventCode' => $content];
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    private function getEventName($name){
        $origin = $this->rootNamespace(). EventBusRabbitMQ::$NAME_SPACE.'\Events\\';
        $array = explode($origin, $name);
        return str_replace('\\', '/', end($array));
    }

    private function clean($code)
    {
        // remove <?php
        $code = str_replace('<?php', '', $code);
        // remove namespace
        $code = preg_replace("/namespace [\S]*/", "", $code);

        return $code;
    }
}