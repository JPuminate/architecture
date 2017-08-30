<?php

namespace JPuminate\Architecture\EventBus\Console\Commands;

use Illuminate\Console\Command;
use JPuminate\Architecture\EventBus\Connections\ConnectionConfiguration;
use JPuminate\Architecture\EventBus\Connections\ConnectionFactory;
use JPuminate\Contracts\EventBus\EventBus;
use RuntimeException;

/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 28/08/2017
 * Time: 20:18
 */

class EventBustListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'eventbus:list';

    protected $description = 'List all supported event';

    protected $eventBus;



    public function __construct(EventBus $eventBus)
    {
        parent::__construct();
        $this->eventBus = $eventBus;
    }

    public function handle(){
      $events = $this->eventBus->getEventResolver()->getAllEvents();
      echo "All events supported by connected eventbus are : \n\n";
      foreach ($events as $event){
          echo "--  ".$event."\n";
      }
    }

}