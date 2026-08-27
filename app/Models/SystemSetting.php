<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    private const RETIREMENT_AGE_CACHE_KEY = 'system-settings.retirement-age';

    private const THEME_CACHE_KEY = 'system-settings.theme';

    public const RETIREMENT_AGE = 'retirement_age';

    public const THEME_MODE = 'theme_mode';

    public const THEME_PRIMARY_LIGHT = 'theme_primary_light';

    public const THEME_PRIMARY_DARK = 'theme_primary_dark';

    public const THEME_ACCENT_LIGHT = 'theme_accent_light';

    public const THEME_ACCENT_DARK = 'theme_accent_dark';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCachedValues());
        static::deleted(fn () => static::forgetCachedValues());
    }

    public static function retirementAge(): int
    {
        return Cache::rememberForever(
            self::RETIREMENT_AGE_CACHE_KEY,
            fn (): int => (int) static::query()->whereKey(self::RETIREMENT_AGE)->value('value') ?: 59,
        );
    }

    /**
     * @return array{mode: string, primary_light: string, primary_dark: string, accent_light: string, accent_dark: string}
     */
    public static function theme(): array
    {
        return Cache::rememberForever(self::THEME_CACHE_KEY, function (): array {
            $settings = static::query()
                ->whereIn('key', [
                    self::THEME_MODE,
                    self::THEME_PRIMARY_LIGHT,
                    self::THEME_PRIMARY_DARK,
                    self::THEME_ACCENT_LIGHT,
                    self::THEME_ACCENT_DARK,
                ])
                ->pluck('value', 'key');

            return [
                'mode' => (string) $settings->get(self::THEME_MODE, 'system'),
                'primary_light' => (string) $settings->get(self::THEME_PRIMARY_LIGHT, '#047857'),
                'primary_dark' => (string) $settings->get(self::THEME_PRIMARY_DARK, '#34d399'),
                'accent_light' => (string) $settings->get(self::THEME_ACCENT_LIGHT, '#0f766e'),
                'accent_dark' => (string) $settings->get(self::THEME_ACCENT_DARK, '#5eead4'),
            ];
        });
    }

    public static function forgetCachedValues(): void
    {
        Cache::forget(self::RETIREMENT_AGE_CACHE_KEY);
        Cache::forget(self::THEME_CACHE_KEY);
    }
}
