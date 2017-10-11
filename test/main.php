<?php

require __DIR__.'/../vendor/autoload.php';


$event = new \Test\Events\Account\AccountChangedCreditEvent(5, 6);

dd($event);