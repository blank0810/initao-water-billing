<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeItem extends Model
{
    protected $table = 'ChargeItem';

    protected $primaryKey = 'charge_item_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'default_amount',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
    ];

    /**
     * Get the customer charges for the charge item
     */
    public function customerCharges()
    {
        return $this->hasMany(CustomerCharge::class, 'charge_item_id', 'charge_item_id');
    }
}
