<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerAddress extends Model
{
    protected $table = 'consumer_addresses';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'house_number',
        'street_name',
        'subdivision_name',
        'barangay_id',
        'purok_id',
        'landmark',
        'zip_code',
        'latitude',
        'longitude',
        'status_id'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function customersWithBillingAddress()
    {
        return $this->hasMany(Customer::class, 'billing_address_id');
    }

    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class, 'service_address_id');
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'service_address_id');
    }
}
