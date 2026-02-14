<?php

namespace Modules\Bookmark\Providers;

use Illuminate\Support\ServiceProvider;

class BookmarkServiceProvider extends ServiceProvider
{
    protected string $name = 'Bookmark';

    protected string $nameLower = 'bookmark';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path($this->nameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->name, 'config/config.php'),
            $this->nameLower
        );
    }
}
