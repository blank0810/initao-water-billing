<?php

namespace App\Services\Admin\Config;

use App\Models\SystemSetting;

class AutomationSettingService
{
    public function getAutomationSettings(): array
    {
        return SystemSetting::where('group', 'automation')
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => [
                'value' => match ($s->type) {
                    'boolean' => filter_var($s->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $s->value,
                    default => $s->value,
                },
                'type' => $s->type,
                'description' => $s->description,
            ]])
            ->toArray();
    }

    public function updateSetting(string $key, mixed $value): array
    {
        $setting = SystemSetting::where('key', $key)->where('group', 'automation')->first();

        if (! $setting) {
            return ['success' => false, 'message' => 'Setting not found.'];
        }

        $setting->update(['value' => (string) $value]);

        return ['success' => true, 'message' => 'Setting updated.'];
    }
}
