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
        ],

        'rabbit2' => [
            'driver' => 'rabbitmq',
            'host' => env('EVENTBUS_HOST', '127.0.0.1'),
            'port' => env('EVENTBUS_PORT', '5674'),
            'username' => env('EVENTBUS_USERNAME', 'guest'),
            'password' => env('EVENTBUS_PASSWORD', 'guest'),
            'manager' => 'default',
            'factory' => 'default'
        ]
    ],

    'subscription' => [
        'manager' => 'in_memory',
        'events' => [
            'resolver' => 'github',
        ],
        'handlers' => [
        ]
    ],

    'resolvers' => [
        'github' => [
            'username' => '',
            'repository' => '',
            'path' => '',
            'reference' => 'master',
            'pattern' => "/^[a-zA-Z][\s\S]+Event$/"
        ]
    ],

    'async' => [
        'queue' => 'default',
        'connection' => 'database'
    ]
];