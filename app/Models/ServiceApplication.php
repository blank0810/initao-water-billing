<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApplication extends Model
{
    protected $table = 'service_applications';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'service_address_id',
        'account_type_id',
        'zone_id',
        'submitted_at',
        'is_printed',
        'status_id'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_printed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function connection()
    {
        return $this->hasOne(ServiceConnection::class, 'application_id');
    }
}
