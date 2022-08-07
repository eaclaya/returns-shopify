<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Shopify\Auth\FileSessionStorage;
use Shopify\Context;

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
        Context::initialize(
            config('services.shopify.key'),
            config('services.shopify.password'),
            'write_draft_orders,read_orders',//scopes
            config('services.shopify.host'),
            new FileSessionStorage('/tmp/php_sessions'),
            config('services.shopify.version'),//'2021-10',//apiVersion
            false,//isEmbeddedApp
            false,//isPrivateApp
	);
    }
}
