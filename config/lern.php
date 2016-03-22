<?php

return [

    'record'=>[
        'table'=>'vendor_tylercd100_lern_exceptions',
    ],

    'notify'=>[
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
            'includeExceptionStackTrace'=>true,
        ],

        /**
         * Pushover settings
         */
        'pushover'=>[
            'token' => env('PUSHOVER_APP_TOKEN'),
            'user'  => env('PUSHOVER_USER_KEY'),
            'sound'=>'siren',
            'includeExceptionStackTrace'=>true,
        ],

        /**
         * Slack settings
         */
        'slack'=>[
            'username'=>'',
            'icon'=>'',
            'channel'=>'',
            'includeExceptionStackTrace'=>true,
        ]
    ],
    
];