<?php

use App\Models\SystemConfig;
use App\Models\Status;
use App\Models\Barangay;
use App\Models\Purok;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\AccountType;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\ReadingZone;
use App\Models\Employee;
use App\Models\Position;
use App\Models\ZoneAssignment;
use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\BillingPeriod;
use App\Models\WaterRate;
use App\Models\WaterBill;
use App\Models\BillAdjustmentType;
use App\Models\BillAdjustment;
use App\Models\ChargeItem;
use App\Models\CustomerCharge;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\LedgerSource;
use App\Models\CustomerLedger;
use App\Models\UserType;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserRole;
use App\Models\AuditLog;

$models = [
    SystemConfig::class, Status::class, Barangay::class, Purok::class, ConsumerAddress::class,
    Customer::class, AccountType::class, ServiceApplication::class, ServiceConnection::class,
    ReadingZone::class, Employee::class, Position::class, ZoneAssignment::class,
    Meter::class, MeterAssignment::class, MeterReading::class, BillingPeriod::class,
    WaterRate::class, WaterBill::class, BillAdjustmentType::class, BillAdjustment::class,
    ChargeItem::class, CustomerCharge::class, Payment::class, PaymentAllocation::class,
    LedgerSource::class, CustomerLedger::class, UserType::class, User::class,
    Role::class, Permission::class, RolePermission::class, UserRole::class, AuditLog::class
];

echo "Testing instantiation of " . count($models) . " models...\n";

foreach ($models as $model) {
    try {
        $instance = new $model();
        echo "✅ " . class_basename($model) . " instantiated successfully.\n";
    } catch (\Throwable $e) {
        echo "❌ " . class_basename($model) . " failed: " . $e->getMessage() . "\n";
    }
}

echo "Done.\n";
