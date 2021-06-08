<?php

namespace CodeOfDigital\LaravelUrlShortener;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class UrlShortenerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/url-shortener.php', 'url-shortener');
        $this->publishAssets();
    }

    protected function publishAssets()
    {
        if (!$this->app->runningInConsole() || Str::contains($this->app->version(), 'lumen'))
            return;

        $this->publishes([__DIR__.'/../config/url-shortener.php' => config_path('url-shortener.php')]);
    }

    public function register()
    {
        $this->app->singleton('url.shortener', function ($app) {
            return new UrlShortenerInstance($app);
        });
    }
}
