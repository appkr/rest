<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed list of social login providers
    |--------------------------------------------------------------------------
    |
    | Corresponding api key and secret should be set at services.php
    |
    */

    'social' => [
        'github',
        'facebook',
        'google',
        'twitter',
        'linkedin'
    ],

    /*
    |--------------------------------------------------------------------------
    | API response settings
    |--------------------------------------------------------------------------
    |
    | includeUser: set whether to include user's account info in login request
    | includeAcl: set whether to include level/role info in login request
    |
    */

    'api' => [
        'includeUser' => true,
        'includeAcl'  => true
    ]

];