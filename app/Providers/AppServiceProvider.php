<?php

namespace App\Providers;

use App\Models\EmailSetting;
use App\Models\SystemSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View as IlluminateView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureDatabaseMailSettings();
        $this->configureFrontendTheme();
    }

    private function configureFrontendTheme(): void
    {
        View::composer(['layouts::app.welcome', 'welcome'], function (IlluminateView $view): void {
            $view->with('frontendTheme', Schema::hasTable('system_settings')
                ? SystemSetting::theme()
                : [
                    'mode' => 'system',
                    'primary_light' => '#047857',
                    'primary_dark' => '#34d399',
                    'accent_light' => '#0f766e',
                    'accent_dark' => '#5eead4',
                ]);
        });
    }

    private function configureDatabaseMailSettings(): void
    {
        if (! Schema::hasTable('email_settings')) {
            return;
        }

        $emailSetting = EmailSetting::query()->latest('id')->first();

        if ($emailSetting !== null) {
            config($emailSetting->mailConfiguration());
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
