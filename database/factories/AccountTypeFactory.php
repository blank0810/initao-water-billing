<?php

namespace Database\Factories;

use App\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountTypeFactory extends Factory
{
    protected $model = AccountType::class;

    public function definition(): array
    {
        return [
            'at_desc' => fake()->randomElement(['Residential', 'Commercial', 'Industrial', 'Institutional']),
            'stat_id' => 2, // ACTIVE
        ];
    }
}
