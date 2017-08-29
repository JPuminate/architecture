<?php

require_once __DIR__ . '/vendor/autoload.php';

use JPuminate\Architecture\EventBus\Events\EventBusWorkerEvent;

class AsyncOperation extends Thread {

    public function __construct($arg) {
        $this->event = new EventBusWorkerEvent();
    }

    public function run() {
       sleep(2);
       var_dump($this->event);
    }

    public function rr(){
        var_dump(10);
    }
}

$async = new AsyncOperation(15);

$async->start();

$async->rr();