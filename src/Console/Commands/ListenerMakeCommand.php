<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use Symfony\Component\Console\Input\InputOption;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class ListenerMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:bus-listener';

    protected $description = 'Create a new bus listener class';

    protected $type = 'EventBusListener';



    protected function getStub(){
        if($this->option('event')) return __DIR__.'\Stubs\listener.event.stub';
        return __DIR__.'\Stubs\listener.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\'.EventBusRabbitMQ::$NAME_SPACE.'\Listeners';
    }

    protected function buildClass($name)
    {
        $replace = [];
        if ($this->option('event'))
            $replace = $this->buildEventReplacements();
        if (is_null($replace)) throw new UnsupportedEvent();
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function getOptions()
    {
        return [
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'Generate a listener for the given event.']
        ];
    }

    private function buildEventReplacements()
    {
        $eventClass = $this->getEventFullNamespace($this->option('event'));
        if (!class_exists($eventClass)) {
            $this->error("A {$eventClass} event does not supported by this eventbus.");
            return null;
        }
        return [
            'DummyEventNamespace' => $eventClass,
            'DummyEventClass' => $this->getEventClass($eventClass)
        ];
    }



    private function getEventFullNamespace($event)
    {
        return app()->getNamespace().EventBusRabbitMQ::$NAME_SPACE.'\\Events\\'.$event;
    }

    private function getEventClass($namespace){
        $array = explode('\\', $namespace);
        return end($array);
    }
}