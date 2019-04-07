<?php

use Monolog\Handler\StreamHandler;
use sammaye\MonologSwiftMailerHandler\Handler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'email'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'email' => [
            'driver' => 'custom',
            'via' => Handler::class,
            'from' => [
                'address' => env('MAIL_LOG_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_LOG_FROM_NAME', 'Example'),
            ],
            'to' => env('MAIL_LOG_EMAIL_ADDRESS', 'hello@example.com'),
        ],

        'scraper' => [
            'driver' => 'custom',
            'via' => danielme85\LaravelLogToDB\LogToDbHandler::class,
            'level' => env('APP_LOG_LEVEL', 'debug'),
            'name' => 'Scraper Log',
            'connection' => 'mongodb',
            'collection' => 'laravel_log',
            'detailed' => true,
            'queue' => false,
            'queue_name' => '',
            'queue_connection' => ''
        ]
    ],

];
