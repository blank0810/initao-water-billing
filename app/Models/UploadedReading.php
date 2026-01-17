<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedReading extends Model
{
    protected $table = 'uploaded_readings';

    protected $primaryKey = 'uploaded_reading_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'schedule_id',
        'connection_id',
        'account_no',
        'customer_name',
        'address',
        'area_desc',
        'account_type_desc',
        'connection_status',
        'meter_serial',
        'previous_reading',
        'arrear',
        'penalty',
        'sequence_order',
        'entry_status',
        'present_reading',
        'reading_date',
        'site_bill_amount',
        'is_printed',
        'is_scanned',
        'user_id',
    ];

    protected $casts = [
        'previous_reading' => 'decimal:3',
        'present_reading' => 'decimal:3',
        'arrear' => 'decimal:2',
        'penalty' => 'decimal:2',
        'site_bill_amount' => 'decimal:2',
        'reading_date' => 'date',
        'is_printed' => 'boolean',
        'is_scanned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the reading schedule associated with this uploaded reading.
     */
    public function schedule()
    {
        return $this->belongsTo(ReadingSchedule::class, 'schedule_id', 'schedule_id');
    }

    /**
     * Get the service connection associated with this uploaded reading.
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the user who uploaded this reading.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Calculate consumption (present reading - previous reading).
     */
    public function getConsumptionAttribute(): ?float
    {
        if ($this->present_reading === null || $this->previous_reading === null) {
            return null;
        }

        return max(0, (float) $this->present_reading - (float) $this->previous_reading);
    }

    /**
     * Scope for readings by schedule.
     */
    public function scopeForSchedule($query, int $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    /**
     * Scope for readings by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for printed readings.
     */
    public function scopePrinted($query)
    {
        return $query->where('is_printed', true);
    }

    /**
     * Scope for scanned readings.
     */
    public function scopeScanned($query)
    {
        return $query->where('is_scanned', true);
    }
}
