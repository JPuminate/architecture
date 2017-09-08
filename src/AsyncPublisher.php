<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 08/09/2017
 * Time: 14:47
 */

namespace JPuminate\Architecture\EventBus;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use JPuminate\Architecture\EventBus\Facades\EventBus;
use JPuminate\Contracts\EventBus\Events\Event;

class AsyncPublisher  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    /**
     * @var Event
     */
    private $event;
    /**
     * @var
     */
    private $logIt;

    public function __construct(Event $event, $logIt)
    {

        $this->event = $event;
        $this->logIt = $logIt;
    }

    public function handle()
    {
        EventBus::publish($this->event, $this->logIt);
    }

}