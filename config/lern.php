<?php

return [

    /**
     * To avoid infinite loops that generate thousands of records/notifications in an instant
     * Please make sure you use a Cache driver that is persistant such as redis, memcache, file, etc
     *
     * Value is in seconds.
     */
    'ratelimit' => 1,

    'record'=>[
        /**
         * The Recorder to use
         */
        'class' => \Tylercd100\LERN\Components\Recorder::class,

        /**
         * The Model to use
         */
        'model' => \Tylercd100\LERN\Models\ExceptionModel::class,

        /**
         * Database connection to use. Null is the default connection.
         */
        'connection'=>null,

        /**
         * Database table to use
         */
        'table'=>'vendor_tylercd100_lern_exceptions',

        /**
         * Information to store
         */
        'collect'=>[
            'method'=>false, //When true it will collect GET, POST, DELETE, PUT, etc...
            'data'=>false, //When true it will collect Input data
            'status_code'=>true,
            'user_id'=>false,
            'url'=>false,
            'ip'=>false,
        ],

        /**
         * When record.collect.data is true, this will exclude certain data keys recursively
         */
        'excludeKeys' => [
            'password'
        ]
    ],

    'notify'=>[
        /**
         * The Notifier to use
         */
        'class' => \Tylercd100\LERN\Components\Notifier::class,

        /**
         * The view file to use
         */
        'view' => 'exceptions.default',

        /**
         * The default name of the monolog logger channel
         */
        'channel'=>'Tylercd100\LERN',

        /**
         * The log level to use when notifying
         */
        'log_level' => 'critical', //Options are: debug, info, notice, warning, error, critical, alert, emergency.

        /**
         * When using the default message body this will also include the stack trace
         */
        'includeExceptionStackTrace' => true,

        /**
         * mail, pushover, slack, etc...
         */
        'drivers'=>['mail'],

        /**
         * Mail settings
         */
        'mail'=>[
            'to'   => 'to@address.com',
            'from' => 'from@address.com',
            'smtp' => true,
        ],

        /**
         * Mailgun settings
         */
        'mailgun'=>[
            'to'    => env('MAILGUN_TO'),
            'from'  => env('MAILGUN_FROM'),
            'token' => env('MAILGUN_APP_TOKEN'),
            'domain'=> env('MAILGUN_DOMAIN'),
        ],

        /**
         * Pushover settings
         */
        'pushover'=>[
            'token' => env('PUSHOVER_APP_TOKEN'),
            'users' => env('PUSHOVER_USER_KEY'),
            'sound' => env('PUSHOVER_SOUND_ERROR', 'siren'), // https://pushover.net/api#sounds
        ],

        /**
         * Slack settings
         */
        'slack'=>[
            'token'   => env('SLACK_APP_TOKEN'), //https://api.slack.com/web#auth
            'channel' => env('SLACK_CHANNEL', '#exceptions'), //Dont forget the '#'
            'username'=> env('SLACK_USERNAME', 'LERN'), //The 'from' name
        ],

        /**
         * Flowdock settings
         */
        'flowdock'=>[
            'token' => env('FLOWDOCK_APP_TOKEN'),
        ],

        /**
         * Fleephook settings
         */
        'fleephook'=>[
            'token' => env('FLEEPHOOK_APP_TOKEN'),
        ],

        /**
         * Plivo settings
         */
        'plivo'=>[
            'auth_id' => env('PLIVO_AUTH_ID'),
            'token'   => env('PLIVO_AUTH_TOKEN'),
            'to'      => env('PLIVO_TO'),
            'from'    => env('PLIVO_FROM'),
        ],

        /**
         * Twilio settings
         */
        'twilio'=>[
            'sid'    => env('TWILIO_AUTH_SID'),
            'secret' => env('TWILIO_AUTH_SECRET'),
            'to'     => env('TWILIO_TO'),
            'from'   => env('TWILIO_FROM'),
        ]
    ],

];
