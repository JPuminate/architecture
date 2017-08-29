<?php

return [

    /*
   |--------------------------------------------------------------------------
   | Default Connection
   |--------------------------------------------------------------------------
   |
   */

    'default' => env('EVENTBUS_CONNECTION', 'rabbit'),

    'connections' => [

        'rabbit' => [
            'driver' => 'rabbitmq',
            'host' => env('EVENTBUS_HOST', '127.0.0.1'),
            'port' => env('EVENTBUS_PORT', '5672'),
            'username' => env('EVENTBUS_USERNAME', 'guest'),
            'password' => env('EVENTBUS_PASSWORD', 'guest'),
            'manager' => 'default',
            'factory' => 'default'
        ]
    ],


    'subscription' => [
        'manager' => 'in_memory',
        'events' => [
            \JPuminate\Architecture\EventBus\Events\EventBusWorkerEvent::class => [
                // handlers
            ],
        ]
    ]
];