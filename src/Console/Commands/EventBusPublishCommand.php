<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use App\EventBus\Events\PingEvent;
use App\EventBus\Events\Users\UserCreatedEvent;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use InvalidArgumentException;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\ConnectionFactory;
use JPuminate\Architecture\EventBus\EventBusRabbitMQ;
use JPuminate\Architecture\EventBus\Exceptions\UnsupportedEvent;
use JPuminate\Contracts\EventBus\EventBus;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class EventBusPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'eventbus:publish  {connection? : The name of connection} {--event=} {--args=}';

    protected $description = 'push a simple ping event to test connectivity or a real event to notify subscribers';

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


    public function handle()
    {
        $this->setConnection();
        if ($this->option('event')) {
            if ($event = $this->supportedEvent()) {
                 if($args = $this->option('args')) {
                     $args = explode(",", $args);
                     $this->eventBus->publish(new $event(...$args));
                 }
                 else $this->eventBus->publish(new $event());
            } else throw new UnsupportedEvent();
        } else {
            $this->eventBus->publish(new PingEvent());
        }
    }


    private function setConnection(){
        $connection = $this->input->getArgument('connection');
       if( $connectionOption = $connection ? $this->laravel['config']['eventbus.connections.'.$connection]
           : $this->laravel['config']['eventbus.connections.'.$this->laravel['config']['eventbus.default']]) {
           $configuration = new ConnectionConfiguration($connectionOption['host'], $connectionOption['port'], $connectionOption['username'], $connectionOption['password']);
           $this->cnx_factory->setConnectionConfiguration($configuration);
       }
       else throw new RuntimeException("connection not found");
    }

    private function supportedEvent()
    {
        if (class_exists($class_name = $this->getLaravel()->getNamespace() .
            EventBusRabbitMQ::$NAME_SPACE . '\Events\\' .
            $this->option('event')))
            return $class_name;
        return false;
    }
}