<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StressTestSeeder extends Seeder
{
    private const COUNT = 3000;

    private const CHUNK = 500;

    private int $activeStatusId;

    private array $purokIds;

    private array $barangayIds;

    private int $townId;

    private int $provinceId;

    private array $areaIds;

    private object $period;

    private array $rates;

    public function run(): void
    {
        $this->command->info('Starting stress test seeder...');
        $start = microtime(true);

        $this->loadPrerequisites();

        DB::beginTransaction();
        try {
            $addressIds = $this->seedAddressPool();
            $customerIds = $this->seedCustomers($addressIds);
            $meterIds = $this->seedMeters();
            $connectionData = $this->seedServiceConnections($customerIds, $addressIds);
            $assignmentIds = $this->seedMeterAssignments($connectionData, $meterIds);
            $readingData = $this->seedMeterReadings($assignmentIds);
            $billIds = $this->seedWaterBillHistory($connectionData, $readingData);
            $this->seedCustomerLedger($connectionData, $billIds);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: '.$e->getMessage());
            throw $e;
        }

        $elapsed = round(microtime(true) - $start, 2);
        $this->command->info("Stress test seeder completed in {$elapsed}s");
        $this->command->table(
            ['Table', 'Records Created'],
            [
                ['consumer_address', count($addressIds)],
                ['customer', self::COUNT],
                ['meter', self::COUNT],
                ['ServiceConnection', self::COUNT],
                ['MeterAssignment', self::COUNT],
                ['MeterReading', self::COUNT * 2],
                ['water_bill_history', self::COUNT],
                ['CustomerLedger', self::COUNT],
            ]
        );
    }

    private function loadPrerequisites(): void
    {
        $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $this->purokIds = DB::table('purok')->pluck('p_id')->toArray();
        $this->barangayIds = DB::table('barangay')->pluck('b_id')->toArray();
        $this->townId = DB::table('town')->value('t_id');
        $this->provinceId = DB::table('province')->value('prov_id');
        $this->areaIds = DB::table('area')->pluck('a_id')->toArray();

        $this->period = DB::table('period')
            ->where('is_closed', false)
            ->where('stat_id', $this->activeStatusId)
            ->orderByDesc('start_date')
            ->first();

        if (! $this->period) {
            throw new \RuntimeException('No active period found. Run PeriodSeeder first.');
        }

        // Load water rates for bill computation
        $rates = DB::table('water_rates')
            ->where('stat_id', $this->activeStatusId)
            ->where(function ($q) {
                $q->where('period_id', $this->period->per_id)
                    ->orWhereNull('period_id');
            })
            ->orderByDesc('period_id')
            ->orderBy('range_id')
            ->get();

        // Group by class_id, prefer period-specific rates
        $this->rates = [];
        foreach ($rates as $rate) {
            $key = $rate->class_id;
            if (! isset($this->rates[$key])) {
                $this->rates[$key] = [];
            }
            // Only add if we haven't already added a period-specific rate for this range
            $rangeKey = $rate->range_id;
            if (! isset($this->rates[$key][$rangeKey])) {
                $this->rates[$key][$rangeKey] = $rate;
            }
        }

        $this->command->info("Prerequisites loaded: Period={$this->period->per_name}, ".count($this->barangayIds).' barangays, '.count($rates).' rate tiers');
    }

    private function seedAddressPool(): array
    {
        $this->command->info('Seeding address pool...');
        $addresses = [];
        $now = now();

        for ($i = 0; $i < 50; $i++) {
            $addresses[] = [
                'p_id' => $this->purokIds[array_rand($this->purokIds)],
                'b_id' => $this->barangayIds[array_rand($this->barangayIds)],
                't_id' => $this->townId,
                'prov_id' => $this->provinceId,
                'stat_id' => $this->activeStatusId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('consumer_address')->insert($addresses);

        return DB::table('consumer_address')
            ->orderByDesc('ca_id')
            ->limit(50)
            ->pluck('ca_id')
            ->toArray();
    }

    private function seedCustomers(array $addressIds): array
    {
        $this->command->info('Seeding '.self::COUNT.' customers...');
        $faker = fake();
        $now = now();
        $rows = [];
        $addressCount = count($addressIds);

        for ($i = 0; $i < self::COUNT; $i++) {
            $rows[] = [
                'create_date' => $now,
                'cust_last_name' => $faker->lastName(),
                'cust_first_name' => $faker->firstName(),
                'cust_middle_name' => $faker->optional(0.7)->lastName(),
                'contact_number' => $faker->optional(0.6)->numerify('09#########'),
                'ca_id' => $addressIds[$i % $addressCount],
                'stat_id' => $this->activeStatusId,
                'c_type' => $i < (self::COUNT * 0.8) ? 'RESIDENTIAL' : 'COMMERCIAL',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('customer')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('customer')->insert($rows);
        }

        return DB::table('customer')
            ->orderByDesc('cust_id')
            ->limit(self::COUNT)
            ->pluck('cust_id')
            ->toArray();
    }

    private function seedMeters(): array
    {
        $this->command->info('Seeding '.self::COUNT.' meters...');
        $brands = ['Neptune', 'Sensus', 'Elster', 'Badger'];
        $now = now();
        $rows = [];

        for ($i = 1; $i <= self::COUNT; $i++) {
            $rows[] = [
                'mtr_serial' => sprintf('STR-2026-%06d', $i),
                'mtr_brand' => $brands[$i % count($brands)],
                'stat_id' => $this->activeStatusId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('meter')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('meter')->insert($rows);
        }

        return DB::table('meter')
            ->where('mtr_serial', 'like', 'STR-2026-%')
            ->orderBy('mtr_id')
            ->pluck('mtr_id')
            ->toArray();
    }

    /**
     * @return array{ids: int[], types: int[], startedDates: string[], customerMap: array<int, int>}
     */
    private function seedServiceConnections(array $customerIds, array $addressIds): array
    {
        $this->command->info('Seeding '.self::COUNT.' service connections...');
        $now = now();
        $addressCount = count($addressIds);
        $areaId = $this->areaIds[0] ?? null;
        $rows = [];
        $types = [];
        $startedDates = [];

        for ($i = 0; $i < self::COUNT; $i++) {
            $isResidential = $i < (self::COUNT * 0.8);
            $typeId = $isResidential ? 1 : 2;
            $startedAt = now()->subDays(rand(30, 730))->toDateString();

            $types[] = $typeId;
            $startedDates[] = $startedAt;

            $rows[] = [
                'account_no' => sprintf('STR-202602-%05d', $i + 1),
                'customer_id' => $customerIds[$i],
                'address_id' => $addressIds[$i % $addressCount],
                'account_type_id' => $typeId,
                'area_id' => $areaId,
                'started_at' => $startedAt,
                'change_meter' => false,
                'stat_id' => $this->activeStatusId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('ServiceConnection')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('ServiceConnection')->insert($rows);
        }

        $connectionIds = DB::table('ServiceConnection')
            ->where('account_no', 'like', 'STR-202602-%')
            ->orderBy('connection_id')
            ->pluck('connection_id')
            ->toArray();

        // Build customer-to-connection map
        $customerMap = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $customerMap[$connectionIds[$i]] = $customerIds[$i];
        }

        return [
            'ids' => $connectionIds,
            'types' => $types,
            'startedDates' => $startedDates,
            'customerMap' => $customerMap,
        ];
    }

    private function seedMeterAssignments(array $connectionData, array $meterIds): array
    {
        $this->command->info('Seeding '.self::COUNT.' meter assignments...');
        $now = now();
        $rows = [];

        for ($i = 0; $i < self::COUNT; $i++) {
            $rows[] = [
                'connection_id' => $connectionData['ids'][$i],
                'meter_id' => $meterIds[$i],
                'installed_at' => $connectionData['startedDates'][$i],
                'install_read' => 0.000,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('MeterAssignment')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('MeterAssignment')->insert($rows);
        }

        return DB::table('MeterAssignment')
            ->whereIn('connection_id', $connectionData['ids'])
            ->orderBy('assignment_id')
            ->pluck('assignment_id')
            ->toArray();
    }

    /**
     * @return array{prevIds: int[], currIds: int[], consumptions: float[]}
     */
    private function seedMeterReadings(array $assignmentIds): array
    {
        $this->command->info('Seeding '.(self::COUNT * 2).' meter readings...');
        $now = now();
        $periodStart = $this->period->start_date;
        $periodEnd = $this->period->end_date;
        $rows = [];

        // Previous readings
        for ($i = 0; $i < self::COUNT; $i++) {
            $rows[] = [
                'assignment_id' => $assignmentIds[$i],
                'period_id' => $this->period->per_id,
                'reading_date' => $periodStart,
                'reading_value' => round(rand(0, 500) + rand(0, 999) / 1000, 3),
                'is_estimated' => false,
                'meter_reader_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('MeterReading')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('MeterReading')->insert($rows);
        }

        // Get the prev reading IDs
        $prevReadings = DB::table('MeterReading')
            ->whereIn('assignment_id', $assignmentIds)
            ->where('reading_date', $periodStart)
            ->orderBy('reading_id')
            ->get(['reading_id', 'reading_value', 'assignment_id'])
            ->keyBy('assignment_id');

        // Current readings
        $consumptions = [];
        $rows = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $prevValue = (float) $prevReadings[$assignmentIds[$i]]->reading_value;
            $consumption = round(rand(1, 50) + rand(0, 999) / 1000, 3);
            $consumptions[] = $consumption;

            $rows[] = [
                'assignment_id' => $assignmentIds[$i],
                'period_id' => $this->period->per_id,
                'reading_date' => $periodEnd,
                'reading_value' => round($prevValue + $consumption, 3),
                'is_estimated' => false,
                'meter_reader_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('MeterReading')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('MeterReading')->insert($rows);
        }

        $currReadingIds = DB::table('MeterReading')
            ->whereIn('assignment_id', $assignmentIds)
            ->where('reading_date', $periodEnd)
            ->orderBy('reading_id')
            ->pluck('reading_id')
            ->toArray();

        $prevReadingIds = $prevReadings->sortBy('assignment_id')->pluck('reading_id')->values()->toArray();

        return [
            'prevIds' => $prevReadingIds,
            'currIds' => $currReadingIds,
            'consumptions' => $consumptions,
        ];
    }

    private function computeBillAmount(float $consumption, int $accountTypeId): float
    {
        $tiers = $this->rates[$accountTypeId] ?? [];

        foreach ($tiers as $rate) {
            if ($consumption >= $rate->range_min && $consumption <= $rate->range_max) {
                return round($consumption * (float) $rate->rate_val, 2);
            }
        }

        // Fallback: use the highest tier
        if (! empty($tiers)) {
            $lastTier = end($tiers);

            return round($consumption * (float) $lastTier->rate_val, 2);
        }

        return 0;
    }

    private function seedWaterBillHistory(array $connectionData, array $readingData): array
    {
        $this->command->info('Seeding '.self::COUNT.' water bills...');
        $now = now();
        $dueDate = date('Y-m-d', strtotime($this->period->end_date.' +15 days'));
        $rows = [];

        for ($i = 0; $i < self::COUNT; $i++) {
            $consumption = $readingData['consumptions'][$i];
            $waterAmount = $this->computeBillAmount($consumption, $connectionData['types'][$i]);

            $rows[] = [
                'connection_id' => $connectionData['ids'][$i],
                'period_id' => $this->period->per_id,
                'prev_reading_id' => $readingData['prevIds'][$i],
                'curr_reading_id' => $readingData['currIds'][$i],
                'consumption' => $consumption,
                'water_amount' => $waterAmount,
                'due_date' => $dueDate,
                'adjustment_total' => 0.00,
                'is_meter_change' => false,
                'stat_id' => $this->activeStatusId,
                'created_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('water_bill_history')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('water_bill_history')->insert($rows);
        }

        return DB::table('water_bill_history')
            ->where('period_id', $this->period->per_id)
            ->whereIn('connection_id', $connectionData['ids'])
            ->orderBy('bill_id')
            ->pluck('bill_id')
            ->toArray();
    }

    private function seedCustomerLedger(array $connectionData, array $billIds): void
    {
        $this->command->info('Seeding '.self::COUNT.' customer ledger entries...');
        $now = now();
        $rows = [];

        // Get the bill amounts for ledger debit
        $bills = DB::table('water_bill_history')
            ->whereIn('bill_id', $billIds)
            ->orderBy('bill_id')
            ->get(['bill_id', 'connection_id', 'water_amount']);

        foreach ($bills as $index => $bill) {
            $customerId = $connectionData['customerMap'][$bill->connection_id] ?? null;
            if (! $customerId) {
                continue;
            }

            $rows[] = [
                'customer_id' => $customerId,
                'connection_id' => $bill->connection_id,
                'period_id' => $this->period->per_id,
                'txn_date' => $now->toDateString(),
                'source_type' => 'BILL',
                'source_id' => $bill->bill_id,
                'description' => 'Water bill - '.$this->period->per_name,
                'debit' => $bill->water_amount,
                'credit' => 0.00,
                'user_id' => null,
                'stat_id' => $this->activeStatusId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= self::CHUNK) {
                DB::table('CustomerLedger')->insert($rows);
                $rows = [];
            }
        }
        if (! empty($rows)) {
            DB::table('CustomerLedger')->insert($rows);
        }
    }
}
