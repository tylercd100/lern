<?php

return [

    'record'=>[
        'table'=>'vendor_tylercd100_lern_exceptions',
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
         * mail, pushover and/or slack
         */
        'drivers'=>['mail'],

        /**
         * Mail settings
         */
        'mail'=>[
            'to'=>'to@address.com',
            'from'=>'from@address.com',
        ],

        /**
         * Pushover settings
         */
        'pushover'=>[
            'token' => env('PUSHOVER_APP_TOKEN'),
            'user'  => env('PUSHOVER_USER_KEY'),
            'sound'=>'siren',
        ],

        /**
         * Slack settings
         */
        'slack'=>[
            'username'=>'',
            'icon'=>'',
            'channel'=>'',
        ]
    ],
    
];