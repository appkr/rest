<?php

get('/', [
    'as'   => 'front',
    'uses' => 'WelcomeController@index'
]);

get('home', [
    'as'   => 'home',
    'uses' => 'HomeController@index'
]);

# Set Locale
get('locale', [
    'as'   => 'global.locale',
    'uses' => 'LocaleController@index'
]);

# Route for Social Login
get('social/{provider}', [
    'as'   => 'social.login',
    'uses' => 'Auth\SocialController@execute'
]);

# Session
$router->group(['prefix' => 'auth', 'as' => 'session.'], function () {
    get('login', [
        'as'   => 'create',
        'uses' => 'Auth\SessionController@getLogin'
    ]);
    post('login', [
        'as'   => 'store',
        'uses' => 'Auth\SessionController@postLogin'
    ]);
    get('logout', [
        'as'   => 'destroy',
        'uses' => 'Auth\SessionController@getLogout'
    ]);
});

# User Registration
$router->group(['prefix' => 'auth', 'as' => 'user.'], function () {
    get('register', [
        'as'   => 'create',
        'uses' => 'Auth\RegisterController@getRegister'
    ]);
    post('register', [
        'as'   => 'store',
        'uses' => 'Auth\RegisterController@postRegister'
    ]);
    get('unregister', [
        'as'   => 'destroy',
        'uses' => 'Auth\RegisterController@destroy'
    ]);
    get('activate/{code}', [
        'as'   => 'activate',
        'uses' => 'Auth\RegisterController@activate'
    ]);
    get('send/{code}', [
        'as'   => 'send-activation',
        'uses' => 'Auth\RegisterController@sendActivation'
    ]);
});

# Password Redminder
$router->group(['prefix' => 'password'], function () {
    get('remind', [
        'as'   => 'reminder.create',
        'uses' => 'Auth\PasswordController@getReminder'
    ]);
    post('remind', [
        'as'   => 'reminder.store',
        'uses' => 'Auth\PasswordController@postReminder'
    ]);
    get('reset/{token}', [
        'as'   => 'reset.create',
        'uses' => 'Auth\PasswordController@getReset'
    ]);
    post('reset', [
        'as'   => 'reset.store',
        'uses' => 'Auth\PasswordController@postReset'
    ]);
});

# User
$router->group(['prefix' => 'user', 'as' => 'user.'], function () {
    get('@{username}', [
        'as'   => 'profile',
        'uses' => 'UserController@profile'
    ]);
    get('lists/{candidate?}', [
        'as'   => 'suggest',
        'uses' => 'UserController@suggest'
    ]);
});

# Routes for API
$router->group(['prefix' => 'api'], function ($router) {
    #Authentication
    get('social/{provider}', [
        'as'   => 'api.social.login',
        'uses' => 'Api\Auth\SocialController@execute'
    ]);

    $router->group(['prefix' => 'auth'], function () {
        # Session
        post('login', [
            'as'   => 'api.session.store',
            'uses' => 'Api\Auth\SessionController@postLogin'
        ]);
        get('logout', [
            'as'   => 'api.session.destroy',
            'uses' => 'Api\Auth\SessionController@getLogout'
        ]);

        #Json Web Token
        get('refresh', [
            'as'   => 'api.token.refresh',
            'uses' => 'Api\Auth\JWTController@refresh'
        ]);

        # User Registration
        post('register', [
            'as'   => 'api.user.store',
            'uses' => 'Api\Auth\RegisterController@postRegister'
        ]);
        post('unregister', [
            'as'   => 'api.user.destroy',
            'uses' => 'Api\Auth\RegisterController@destroy'
        ]);
        get('send/{code}', [
            'as'   => 'api.user.send-activation',
            'uses' => 'Api\Auth\RegisterController@sendActivation'
        ]);
    });

    # Routes for version 1
    $router->group(['prefix' => 'v1'], function () {
        # Todo
        resource(
            'todo',
            'Api\V1\TodoController',
            ['except' => ['create', 'edit']]
        );
    });
});