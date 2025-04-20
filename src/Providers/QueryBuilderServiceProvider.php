<?php

namespace LaravelQueryBuilder\Providers;

use Illuminate\Support\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Регистрация конфига
        $this->mergeConfigFrom(
            __DIR__.'/../../config/query-builder.php',
            'query-builder'
        );
    }

    public function boot()
    {
        // Публикация конфига
        $this->publishes([
            __DIR__.'/../../config/query-builder.php' => config_path('query-builder.php'),
        ], 'query-builder-config');
    }
}