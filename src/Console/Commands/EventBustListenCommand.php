<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\ConnectionFactory;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use JPuminate\Contracts\EventBus\EventBus;
use RuntimeException;
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
    /**
     * @var ConnectionFactory
     */
    private $cnx_factory;


    public function __construct(ConnectionFactory $cnx_factory, EventBus $eventBus)
    {
        parent::__construct();
        $this->eventBus = $eventBus;
        $this->cnx_factory = $cnx_factory;
    }

    public function handle(){
        $events = $this->getPreConfiguredSubscriptions();
        $this->setConnection();
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

    private function setConnection(){
        $connection = $this->input->getArgument('connection');
       if( $connectionOption = $connection ? $this->laravel['config']['eventbus.connections.'.$connection] : $this->laravel['config']['eventbus.default']) {
           $configuration = new ConnectionConfiguration($connectionOption['host'], $connectionOption['port'], $connectionOption['username'], $connectionOption['password']);
           $this->cnx_factory->setConnectionConfiguration($configuration);
       }
       else throw new RuntimeException("connection not found");
    }
}