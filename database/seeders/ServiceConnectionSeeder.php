<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\ServiceConnection;
use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates sample service connections with both Residential and Commercial account types.
     * This seeder also creates the required customers and addresses if they don't exist.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get account types
        $residentialType = AccountType::where('at_desc', 'Residential')->first();
        $commercialType = AccountType::where('at_desc', 'Commercial')->first();

        if (! $residentialType || ! $commercialType) {
            $this->command->error('Account types not found. Please run AccountTypeSeeder first.');

            return;
        }

        // Check if there's at least one address
        $address = ConsumerAddress::first();

        if (! $address) {
            // Create a sample address using existing location data
            $purok = DB::table('purok')->first();
            $barangay = DB::table('barangay')->first();
            $town = DB::table('town')->first();
            $province = DB::table('province')->first();

            if (! $purok || ! $barangay || ! $town || ! $province) {
                $this->command->error('Location data not found. Please run location seeders first (ProvinceSeeder, TownSeeder, BarangaySeeder, PurokSeeder).');

                return;
            }

            $address = ConsumerAddress::create([
                'p_id' => $purok->p_id,
                'b_id' => $barangay->b_id,
                't_id' => $town->t_id,
                'prov_id' => $province->prov_id,
                'stat_id' => $activeStatusId,
            ]);
        }

        // Sample data for service connections
        $serviceConnections = [
            // Residential connections
            [
                'customer' => [
                    'cust_first_name' => 'Juan',
                    'cust_middle_name' => 'Santos',
                    'cust_last_name' => 'Dela Cruz',
                    'c_type' => 'Individual',
                ],
                'account_type' => $residentialType->at_id,
                'account_prefix' => 'RES',
            ],
            [
                'customer' => [
                    'cust_first_name' => 'Maria',
                    'cust_middle_name' => 'Garcia',
                    'cust_last_name' => 'Santos',
                    'c_type' => 'Individual',
                ],
                'account_type' => $residentialType->at_id,
                'account_prefix' => 'RES',
            ],
            [
                'customer' => [
                    'cust_first_name' => 'Pedro',
                    'cust_middle_name' => 'Lopez',
                    'cust_last_name' => 'Reyes',
                    'c_type' => 'Individual',
                ],
                'account_type' => $residentialType->at_id,
                'account_prefix' => 'RES',
            ],
            // Commercial connections
            [
                'customer' => [
                    'cust_first_name' => 'ABC',
                    'cust_middle_name' => '',
                    'cust_last_name' => 'Trading Corp',
                    'c_type' => 'Corporation',
                ],
                'account_type' => $commercialType->at_id,
                'account_prefix' => 'COM',
            ],
            [
                'customer' => [
                    'cust_first_name' => 'Initao',
                    'cust_middle_name' => '',
                    'cust_last_name' => 'Hardware Store',
                    'c_type' => 'Business',
                ],
                'account_type' => $commercialType->at_id,
                'account_prefix' => 'COM',
            ],
        ];

        $createdCount = 0;

        foreach ($serviceConnections as $index => $data) {
            // Create customer
            $customer = Customer::create([
                'create_date' => now(),
                'cust_first_name' => $data['customer']['cust_first_name'],
                'cust_middle_name' => $data['customer']['cust_middle_name'],
                'cust_last_name' => $data['customer']['cust_last_name'],
                'ca_id' => $address->ca_id,
                'land_mark' => 'Near main road',
                'stat_id' => $activeStatusId,
                'c_type' => $data['customer']['c_type'],
                'resolution_no' => $this->generateResolutionNumber(
                    $data['customer']['cust_first_name'],
                    $data['customer']['cust_middle_name'],
                    $data['customer']['cust_last_name']
                ),
            ]);

            // Generate unique account number
            $accountNo = $this->generateAccountNumber($data['account_prefix'], $index + 1);

            // Create service connection
            ServiceConnection::create([
                'account_no' => $accountNo,
                'customer_id' => $customer->cust_id,
                'address_id' => $address->ca_id,
                'account_type_id' => $data['account_type'],
                'started_at' => now()->subMonths(rand(1, 12)),
                'ended_at' => null,
                'stat_id' => $activeStatusId,
            ]);

            $createdCount++;
        }

        $this->command->info("Service Connections seeded: {$createdCount} connections created");
        $this->command->info('- Residential: 3 connections');
        $this->command->info('- Commercial: 2 connections');
    }

    /**
     * Generate a unique account number
     */
    private function generateAccountNumber(string $prefix, int $sequence): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    /**
     * Generate a resolution number similar to CustomerHelper
     */
    private function generateResolutionNumber(string $firstName, string $middleName, string $lastName): string
    {
        $initials = strtoupper(
            substr($firstName, 0, 1).
            ($middleName ? substr($middleName, 0, 1) : '').
            substr($lastName, 0, 1)
        );

        return 'INITAO-'.$initials.'-'.time().rand(100, 999);
    }
}
