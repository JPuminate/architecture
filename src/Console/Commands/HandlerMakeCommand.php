<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use Symfony\Component\Console\Input\InputOption;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class HandlerMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:handler';


    protected $description = 'Create a new handler class';


    protected $type = 'EventHandler';

    private $eventsNamespace = 'JPuminate\\Contracts\\EventBus\\Events';



    protected function getStub(){
        if($this->option('event')) return __DIR__.'\Stubs\handler.event.stub';
        return __DIR__.'\Stubs\handler.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\EventBus\Handlers';
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
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'Generate a handler for the given event.']
        ];
    }

    private function buildEventReplacements()
    {
        $eventClass = $this->parseEvent($this->option('event'));
        if (! class_exists($eventNamespace = $this->getEventNamespace($eventClass))) {
            $this->error("A {$eventClass} event does not supported by this eventbus.");
            return null;
        }
        return [
            'DummyEventNamespace', $eventNamespace,
            'DummyEventClass', $eventClass
        ];
    }

    private function getEventNamespace($event_class){
        return $this->eventsNamespace.'\\'.$event_class;
    }

    private function parseEvent($event)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $event)) {
            throw new InvalidArgumentException('Event name contains invalid characters.');
        }

        $event = trim(str_replace('/', '\\', $event), '\\');

        return $event;
    }
}