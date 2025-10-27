<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use AshAllenDesign\ConfigValidator\Services\ConfigValidator;
use AshAllenDesign\ConfigValidator\Services\Rule;
use AshAllenDesign\EmailUtilities\Exceptions\ValidationException;
use Illuminate\Support\ServiceProvider;

class EmailUtilitiesProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(path: __DIR__.'/../config/email-utilities.php', key: 'email-utilities');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/email-utilities.php' => config_path('email-utilities.php'),
        ], groups: ['email-utilities-config']);

        $this->publishes([
            __DIR__.'/../lists/disposable-domains.json' => base_path('disposable-domains.json'),
            __DIR__.'/../lists/role-accounts.json' => base_path('role-accounts.json'),
        ], groups: ['email-utilities-lists']);

        $this->validateConfig();
    }

    private function validateConfig(): void
    {
        if (!config()->boolean('email-utilities.validate_config', default: false)) {
            return;
        }

        $validator = app(ConfigValidator::class);

        $passes = $validator
            ->throwExceptionOnFailure(false)
            ->runInline([
                'email-utilities' => [
                    Rule::make('disposable_email_list_path')->rules(['nullable', 'string']),
                    Rule::make('role_accounts_list_path')->rules(['nullable', 'string']),
                ],
            ]);

        if (! $passes) {
            $errorKey = (string) array_key_first($validator->errors());

            $validationMessage = $validator->errors()[$errorKey][0];

            throw new ValidationException($validationMessage);
        }
    }
}
