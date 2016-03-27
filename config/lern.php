<?php

return [

    'record'=>[
        'table'=>'vendor_tylercd100_lern_exceptions',
        'collect'=>[
            'method'=>false,//When true it will collect GET, POST, DELETE, PUT, etc...
            'data'=>false,//When true it will collect Input data
            'user_id'=>false,
            'url'=>false,
        ],
    ],

    'notify'=>[
        /**
         * The default name of the monolog logger channel
         */
        'channel'=>'Tylercd100\LERN',

        /**
         * When using the default message body this will also include the stack trace
         */
        'includeExceptionStackTrace'=>true,
        
        /**
         * mail, pushover, slack, etc...
         */
        'drivers'=>['mail'],

        /**
         * Mail settings
         */
        'mail'=>[
            'to'  =>'to@address.com',
            'from'=>'from@address.com',
        ],

        /**
         * Pushover settings
         */
        'pushover'=>[
            'token' => env('PUSHOVER_APP_TOKEN'),
            'user'  => env('PUSHOVER_USER_KEY'),
            'sound' => 'siren',
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
         * HipChat settings
         */
        'hipchat'=>[
            'token' => env('HIPCHAT_APP_TOKEN'),
            'room'  => 'room',
            'name'  => 'name',
            'notify'=> true,
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
