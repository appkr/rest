<?php

$composer = file_get_contents(base_path('composer.json'));

return json_decode($composer, true);

//return [
//
//    'canonical'   => 'appkr/rest',
//    'name'        => 'Restful',
//    'description' => 'General-purpose Restful API Service',
//    'author'      => [
//        'name'  => 'Appkr',
//        'email' => 'juwonkim@me.com'
//    ]
//
//];
