<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeItem extends Model
{
    protected $table = 'ChargeItem';

    protected $primaryKey = 'charge_item_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_amount',
        'charge_type',
        'is_taxable',
        'stat_id',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    /**
     * Get the customer charges for the charge item
     */
    public function customerCharges(): HasMany
    {
        return $this->hasMany(CustomerCharge::class, 'charge_item_id', 'charge_item_id');
    }

    /**
     * Get the status of the charge item
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Scope for active one-time application fees
     */
    public function scopeApplicationFees(Builder $query): Builder
    {
        return $query->where('charge_type', 'one_time')
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->whereIn('code', ['CONN_FEE', 'SERVICE_DEP', 'METER_DEP', 'APP_PROC', 'INSTALL_FEE']);
    }

    /**
     * Scope for active charges only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('stat_id', Status::getIdByDescription(Status::ACTIVE));
    }
}
