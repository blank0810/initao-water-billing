<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => SystemSetting::AUTO_CREATE_PERIOD,
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'automation',
                'description' => 'Automatically create next month period on the last day of each month',
            ],
            [
                'key' => SystemSetting::AUTO_APPLY_PENALTIES,
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'automation',
                'description' => 'Automatically apply late payment penalties daily to overdue bills',
            ],
            [
                'key' => SystemSetting::AUTO_CLOSE_READING_SCHEDULE,
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'automation',
                'description' => 'Auto-complete reading schedules when all entries are read',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('System settings seeded: '.count($settings).' settings');
    }
}
