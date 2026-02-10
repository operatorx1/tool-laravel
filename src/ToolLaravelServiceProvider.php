<?php
namespace ToolLaravel;

use Illuminate\Support\ServiceProvider;
use ToolLaravel\Commands\MakeViewCustom;
use ToolLaravel\Commands\MakeControllerCustom;
use ToolLaravel\Commands\MakeControllerViewCustom;

class ToolLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Registrasi custom command
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeViewCustom::class,
                MakeControllerCustom::class,
                MakeControllerViewCustom::class,
            ]);

            // Publikasikan stub jika perlu
            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs'),
            ], 'tool-laravel');
        }
    }

    public function register()
    {
        //
    }
}
