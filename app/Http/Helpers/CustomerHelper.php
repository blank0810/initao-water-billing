<?php

namespace App\Http\Helpers;

use App\Models\ConsumerAddress;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerHelper
{
  /**
   * Generate a customer resolution number with pattern: INITAO-{NAME_INITIALS}-{DATETIMESTAMP}
   * 
   * @param string $firstName
   * @param string $lastName
   * @return string
   */
  public static function generateCustomerResolutionNumber(string $firstName, string $lastName): string
  {
    $initials = strtoupper(
      substr($firstName, 0, 1) .
        substr($lastName, 0, 1)
    );

    $timestamp = now()->format('YmdHis');

    return "INITAO-{$initials}-{$timestamp}";
  }

  /**
   * Generate a unique customer ID
   */
  public static function generateCustomerId(string $firstName, string $lastName): string
  {
    $datePrefix = now()->format('Ymd');
    $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $initials = strtoupper(
      substr($firstName, 0, 1) .
        substr($lastName, 0, 1)
    );

    return "{$datePrefix}-{$randomDigits}-{$initials}";
  }

  /**
   * Create consumer address
   */
  public static function createConsumerAddress(array $data): ConsumerAddress
  {
    return ConsumerAddress::create([
      'p_id' => $data['p_id'] ?? null,
      'b_id' => $data['b_id'] ?? null,
      't_id' => $data['t_id'] ?? 1,
      'prov_id' => $data['prov_id'] ?? 1,
      'stat_id' => $data['address_stat_id'] ?? null,
    ]);
  }

  /**
   * Create customer record
   * 
   * @param array $data Customer data
   * @param int $addressId Address ID from consumer_address table
   * @return \App\Models\Customer
   */
  public static function createCustomer(array $data, int $addressId): Customer
  {
    return Customer::create([
      'cust_last_name' => $data['cust_last_name'],
      'cust_first_name' => $data['cust_first_name'],
      'cust_middle_name' => $data['cust_middle_name'] ?? null,
      'ca_id' => $addressId,
      'land_mark' => $data['land_mark'] ?? null,
      'stat_id' => $data['stat_id'] ?? null,
      'c_type' => $data['c_type'] ?? null,
      'resolution_no' => $data['resolution_no'] ?? null,
      'create_date' => now(),
    ]);
  }
}
