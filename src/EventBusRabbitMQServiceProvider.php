<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 27/08/2017
 * Time: 12:54
 */

namespace JPuminate\Architecture\EventBus;


use Illuminate\Support\ServiceProvider;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\ConnectionFactory;
use JPuminate\Architecture\EventBus\Connections\DefaultConnectionFactory;
use JPuminate\Architecture\EventBus\Connections\DefaultRabbitMQConnectionManager;
use JPuminate\Architecture\EventBus\Connections\RabbitMQConnectionManager;
use JPuminate\Architecture\EventBus\Console\Commands\EventBustListenCommand;
use JPuminate\Architecture\EventBus\Console\Commands\ListenerMakeCommand;
use JPuminate\Contracts\EventBus\EventBus;
use JPuminate\Contracts\EventBus\Subscriptions\InMemoryEventBusSubscriptionManager;
use Psr\Log\LoggerInterface;

require_once __DIR__.'\..\vendor\autoload.php';

class EventBusRabbitMQServiceProvider extends ServiceProvider
{

    public static $configFile = 'eventbus';



    public function boot(){
        if($this->app->runningInConsole()){
            $this->registerCommands();
            $this->_publishes();
        }
    }

    private function _publishes(){
        $this->publishes([
            __DIR__ . '/../resources/eventbus.php' => config_path('eventbus.php'),
        ], 'config');
    }

    private function registerCommands(){
        $this->commands([
            ListenerMakeCommand::class,
            EventBustListenCommand::class
        ]);
    }

    public function register(){
        $config = config(static::$configFile);
        $this->registerConnectionManager($config);
        $this->registerEventBusInstance($config);
        $this->registerEventBusManager();
    }


    private function registerConnectionManager($config)
    {
        $connectionName = $config['default'];
        $connectionOption = $config['connections'][$connectionName];
        $connection = new ConnectionConfiguration($connectionOption['host'], $connectionOption['port'], $connectionOption['username'], $connectionOption['password']);
        $connectionFactory = $connectionOption['factory'] == "default" ? new DefaultConnectionFactory($connection)
            : new  $connectionOption['factory']($connection);
        $logger = $this->app->make(LoggerInterface::class);
        $connectionManager = $connectionOption['manager'] == 'default' ? new DefaultRabbitMQConnectionManager($connectionFactory, $logger)
            : new $connectionOption['manager']($connectionFactory, $logger);
        $this->app->singleton(RabbitMQConnectionManager::class, function() use ($connectionManager) {
            return $connectionManager;
        });
        $this->app->singleton(ConnectionFactory::class, function() use($connectionFactory){
            return $connectionFactory;
        });
    }

    private function registerEventBusInstance($config){

        $subscription_manager_driver = $config['subscription']['manager'];
        $subscription_manager = $subscription_manager_driver == "in_memory" ? new InMemoryEventBusSubscriptionManager() : new $subscription_manager();

        $this->app->singleton(EventBus::class, function () use ($subscription_manager){
            return new EventBusRabbitMQ(
                $this->app->make(RabbitMQConnectionManager::class),
                $this->app->make(LoggerInterface::class),
                $subscription_manager,
                new ContainerBasedHandlerMaker()
                );
        });
    }

    private function registerEventBusManager(){
        $this->app->singleton(EventBusManager::class, function ($app) {
            return new EventBusManager($app);
        });
    }


}