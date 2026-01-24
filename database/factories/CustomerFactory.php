<?php

namespace Database\Factories;

use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'cust_first_name' => strtoupper($firstName),
            'cust_middle_name' => strtoupper(fake()->lastName()),
            'cust_last_name' => strtoupper($lastName),
            'contact_number' => fake()->numerify('09#########'),
            'ca_id' => ConsumerAddress::factory(),
            'land_mark' => strtoupper(fake()->streetName()),
            'c_type' => fake()->randomElement(['RESIDENTIAL', 'COMMERCIAL', 'INDUSTRIAL']),
            'resolution_no' => 'INITAO-' . strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 2)) . '-' . fake()->numerify('##########'),
            'create_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }

    /**
     * Indicate that the customer is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'stat_id' => Status::getIdByDescription(Status::PENDING) ?? 1,
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'stat_id' => Status::getIdByDescription(Status::INACTIVE) ?? 2,
        ]);
    }

    /**
     * Create customer with specific name for search testing.
     */
    public function withName(string $firstName, string $lastName, ?string $middleName = null): static
    {
        return $this->state(fn (array $attributes) => [
            'cust_first_name' => strtoupper($firstName),
            'cust_middle_name' => $middleName ? strtoupper($middleName) : null,
            'cust_last_name' => strtoupper($lastName),
            'resolution_no' => 'INITAO-' . strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 2)) . '-' . fake()->numerify('##########'),
        ]);
    }
}
