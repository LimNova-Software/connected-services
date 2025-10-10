<?php

declare(strict_types=1);

namespace LimNova\ApiManager\Providers;

use Illuminate\Support\ServiceProvider;
use LimNova\ApiManager\Contracts\FactoryInterface;
use LimNova\ApiManager\Core\ConnectorFactory;

final class ApiManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FactoryInterface::class, ConnectorFactory::class);
        $this->app->singleton(ConnectorFactory::class);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/api-manager.php',
            'api-manager'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/api-manager.php' => $this->app->configPath('api-manager.php'),
            ], 'api-manager-config');
        }
    }

    public function provides(): array
    {
        return [
            FactoryInterface::class,
            ConnectorFactory::class,
        ];
    }
}
