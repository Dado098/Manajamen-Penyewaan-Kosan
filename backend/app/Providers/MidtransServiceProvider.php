<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans\Snap;
use Midtrans\Veritrans;

class MidtransServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Snap::class, function ($app) {
            return new Snap(config('midtrans.server_key'), config('midtrans.is_production'));
        });
    }

    public function boot()
    {
        //
    }
}
