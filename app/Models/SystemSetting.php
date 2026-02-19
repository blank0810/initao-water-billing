<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public const AUTO_CREATE_PERIOD = 'auto_create_period';

    public const AUTO_APPLY_PENALTIES = 'auto_apply_penalties';

    public const AUTO_CLOSE_READING_SCHEDULE = 'auto_close_reading_schedule';

    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * Get a setting value by key, with type casting.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value): bool
    {
        return (bool) static::where('key', $key)->update(['value' => (string) $value]);
    }

    /**
     * Check if a boolean setting is enabled.
     */
    public static function isEnabled(string $key): bool
    {
        return (bool) static::getValue($key, false);
    }
}
