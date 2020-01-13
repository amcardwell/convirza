<?php

return [
    'username' => env('CONVIRZA_USER'),
    'password' => env('CONVIRZA_PASS'),
    'debug' => env('CONVIRZA_DEBUG', false),

    'cache' => [
    	'store' => null,
    	'duration' => 30 // In seconds
    ]
];
