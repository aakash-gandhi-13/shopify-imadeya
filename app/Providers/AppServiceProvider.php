<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(255);
        if (!is_null(request()->header('locale')) && in_array(request()->header('locale'), config('constants.allowedLocales'))) {
            app()->setLocale(request()->header('locale'));
        }
    }
}
