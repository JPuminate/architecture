<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use JPuminate\Contracts\EventBus\EventBus;
use Symfony\Component\Console\Input\InputOption;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class EventBustListenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'eventbus:listen  {connection? : The name of connection}';

    protected $description = 'Listen to a published events';

    protected $eventBus;


    public function __construct(EventBus $eventBus)
    {
        parent::__construct();
        $this->eventBus = $eventBus;
    }

    public function handle(){
        $events = $this->getPreConfiguredSubscriptions();
        foreach ($events as $event => $handlers){
            foreach ($handlers as $handler) {
                $this->eventBus->subscribe($event, $handler);
            }
        }
        $this->line("<info>EventBus start listening ...</info>");
        $this->eventBus->start();
        $this->eventBus->listen();
    }

    private function getPreConfiguredSubscriptions(){
        return $this->laravel['config']['eventbus.subscription.events'];
    }
}