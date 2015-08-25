<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default role
    |--------------------------------------------------------------------------
    |
    | String value of default role for new comer
    |
    */

    'defaultRole'   => 'visitor',

    /*
    |--------------------------------------------------------------------------
    | Allowed Models
    |--------------------------------------------------------------------------
    |
    | List of Models into which the permissions should apply
    |
    */

    'allowedModels' => [
        \App\Todo::class,
        \Appkr\Fractal\Example\Resource::class
    ],

    /*
    |--------------------------------------------------------------------------
    | List of roles
    |--------------------------------------------------------------------------
    |
    | This will be used at RolesTableSeeder
    | ex. ['slug' => 'level']
    |
    */

    'roles'         => [
        'visitor'    => 1,
        'member'     => 11,
        'subscriber' => 21,
        'admin'      => 81,
        'root'       => 99
    ],

    /*
    |--------------------------------------------------------------------------
    | List of permissions
    |--------------------------------------------------------------------------
    |
    | This will be used at PermissionsTableSeeder
    | ex. ['role' => ['permission', ...]]
    |
    */

    'permissions'   => [
        'visitor'    => ['read'],
        'member'     => ['create', 'update', 'delete'],
        'subscriber' => ['download']
    ],

];