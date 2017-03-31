<?php

namespace NaspersClassifieds\LaravelVersionedSeeder;

use Illuminate\Support\ServiceProvider;
use NaspersClassifieds\LaravelVersionedSeeder\Console\Commands\ResetCommand;
use NaspersClassifieds\LaravelVersionedSeeder\Console\Commands\SeedCommand;
use NaspersClassifieds\LaravelVersionedSeeder\Console\Commands\SeedMakeCommand;
use NaspersClassifieds\LaravelVersionedSeeder\Console\Commands\StatusCommand;

class VersionedSeederServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        dd('here');
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('seed:reset', function () {
            return new ResetCommand();
        });

        $this->app->singleton('seed', function () {
            return new SeedCommand();
        });

        $this->app->singleton('seed:make', function () {
            return new SeedMakeCommand();
        });

        $this->app->singleton('seed:status', function () {
            return new StatusCommand();
        });

        $this->commands(
            'seed',
            'seed:reset',
            'seed:make',
            'seed:status'
        );
    }

    public function provides()
    {
        return [
            'seed',
            'seed:reset',
            'seed:make',
            'seed:status'
        ];
    }
}
