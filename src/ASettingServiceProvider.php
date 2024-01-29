<?php

namespace AuroraWebSoftware\ASetting;

use AuroraWebSoftware\ASetting\Commands\ASettingCommand;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ASettingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Yayınlamaları yüklemek için
        $this->registerPublishing();

        // Migration'ları yüklemek için
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Route'ları yüklemek için
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Validator'lar için özel kuralları tanımlamak için
        $this->registerCustomValidators();
    }

    public function register()
    {
        // Singleton olarak ASetting örneğini kaydetmek için
        $this->app->singleton('asetting', function ($app) {
            return new ASetting();
        });

        // Config dosyasını birleştirmek için
        $this->mergeConfigFrom(__DIR__.'/../config/asetting.php', 'asetting');
    }

    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            // Migration'ları yayınlamak için
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'asetting-migrations');

            // Config dosyasını yayınlamak için
            $this->publishes([
                __DIR__.'/../config/asetting.php' => config_path('asetting.php'),
            ], 'asetting-config');
        }
    }

    private function registerCustomValidators()
    {
        // Özel bir string_or_array validator'ü tanımlamak için
        $this->app['validator']->extend('string_or_array', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) || is_array($value);
        });

        // Özel bir string_or_int_array_bool validator'ü tanımlamak için
        $this->app['validator']->extend('string_or_int_array_bool', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) || is_array($value) || is_int($value) || is_bool($value);
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('asetting')
            ->hasConfigFile('asetting')
            ->hasRoute('api')
            ->hasViews()
            ->hasMigration('create_asetting_table')
            ->hasCommand(ASettingCommand::class);
    }
}

