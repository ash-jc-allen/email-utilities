<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use Illuminate\Support\ServiceProvider;

class EmailUtilitiesProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/email-utilities.php' => config_path('email-utilities.php'),
        ], groups: ['email-utilities-config']);

        $this->publishes([
            __DIR__.'/../lists/disposable-domains.json' => base_path('disposable-domains.json'),
        ], groups: ['email-utilities-lists']);
    }
}
