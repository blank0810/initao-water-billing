<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceConnection extends Model
{
    protected $table = 'service_connections';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'account_number',
        'application_id',
        'customer_id',
        'service_address_id',
        'account_type_id',
        'zone_id',
        'started_at',
        'ended_at',
        'status_id'
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(ServiceApplication::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function serviceAddress()
    {
        return $this->belongsTo(ConsumerAddress::class, 'service_address_id');
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function zone()
    {
        return $this->belongsTo(ReadingZone::class, 'zone_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function meterAssignments()
    {
        return $this->hasMany(MeterAssignment::class, 'connection_id');
    }

    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'connection_id');
    }
}
