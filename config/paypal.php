<?php
return [
    'client_id' => env('PAYPAL_CLIENT_ID',''),
    'secret_key' => env('PAYPAL_SECRET_KEY',''),
    'settings' => [
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ],
];
