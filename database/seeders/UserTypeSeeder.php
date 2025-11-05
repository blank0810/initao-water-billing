<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $userTypes = [
      [
        'ut_id' => 3,
        'ut_desc' => 'ADMIN',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'ut_id' => 4,
        'ut_desc' => 'BILLING',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ];

    // Use updateOrInsert to prevent duplicate entries
    foreach ($userTypes as $userType) {
      DB::table('user_types')->updateOrInsert(
        ['ut_id' => $userType['ut_id']],
        $userType
      );
    }

    $this->command->info('User types seeded: ' . count($userTypes) . ' user types');
  }
}
