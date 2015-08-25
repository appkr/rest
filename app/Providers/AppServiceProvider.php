<?php

namespace App\Providers;

use Crypt;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Optimus\Optimus;
use Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($locale = Request::cookie('locale')) {
            $locale = Crypt::decrypt($locale);
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register Optimus id transformer
        $this->app->singleton(Optimus::class, function () {
            return new Optimus(1118129599, 664904255, 1002004882);
        });

        if ($this->app->environment('local')) {
            $this->app->register(\Spatie\Tail\TailServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
