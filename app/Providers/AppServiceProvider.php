<?php

namespace App\Providers;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        /*$request = app('request');

        // ALLOW OPTIONS METHOD
        if($request->getMethod() === 'OPTIONS')  {
            app()->options($request->path(), function () {
                return response('OK',200)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods','OPTIONS, GET, POST, PUT, DELETE')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Origin');
            });
        }*/
    }

    public function boot()
    {
        // Define a macro to add domain_id column to every table
        Blueprint::macro('addDomainId', function () {
            $this->unsignedBigInteger('domain_id')->nullable()->index();
        });

        // Automatically add domain_id to every table
        Schema::defaultStringLength(191);

        // You can also include other bootstrapping code here
    }
}
