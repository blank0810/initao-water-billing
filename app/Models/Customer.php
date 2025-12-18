<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'email',
        'billing_address_id',
        'status_id',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function billingAddress()
    {
        return $this->belongsTo(ConsumerAddress::class, 'billing_address_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class);
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class);
    }

    public function customerCharges()
    {
        return $this->hasMany(CustomerCharge::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(CustomerLedger::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
