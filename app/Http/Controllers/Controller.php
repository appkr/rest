<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

abstract class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Constructor
     */
    public function __construct() {
        $this->setSharedVariables();
    }

    /**
     * Share common view variables
     */
    protected function setSharedVariables() {
        view()->share('currentLocale', app()->getLocale());
        view()->share('currentUser', auth()->user());
        view()->share('currentRouteName', \Route::currentRouteName());
        view()->share('currentUrl', \Request::fullUrl());
    }
}
