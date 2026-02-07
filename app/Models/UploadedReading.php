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
        'computed_amount',
        'is_meter_change',
        'removal_read',
        'install_read',
        'is_printed',
        'is_scanned',
        'photo_path',
        'is_processed',
        'processed_at',
        'processed_by',
        'bill_id',
        'user_id',
    ];

    protected $casts = [
        'previous_reading' => 'decimal:3',
        'present_reading' => 'decimal:3',
        'arrear' => 'decimal:2',
        'penalty' => 'decimal:2',
        'site_bill_amount' => 'decimal:2',
        'computed_amount' => 'decimal:2',
        'is_meter_change' => 'boolean',
        'removal_read' => 'decimal:3',
        'install_read' => 'decimal:3',
        'reading_date' => 'date',
        'is_printed' => 'boolean',
        'is_scanned' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
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
     * Get the user who processed this reading.
     */
    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    /**
     * Get the generated water bill.
     */
    public function waterBill()
    {
        return $this->belongsTo(WaterBillHistory::class, 'bill_id', 'bill_id');
    }

    /**
     * Calculate consumption accounting for meter change scenarios.
     *
     * Normal: present_reading - previous_reading
     * Meter change: (present_reading - install_read) + (removal_read - previous_reading)
     *
     * Returns null if required fields are missing to distinguish "zero consumption"
     * from "data not yet available".
     */
    public function getConsumptionAttribute(): ?float
    {
        if ($this->present_reading === null || $this->previous_reading === null) {
            return null;
        }

        if ($this->is_meter_change) {
            // For meter change, both install_read and removal_read are required
            // Return null if either is missing to signal incomplete data
            if ($this->install_read === null || $this->removal_read === null) {
                return null;
            }

            // New meter: present_reading - install_read
            $newMeterConsumption = max(0, (float) $this->present_reading - (float) $this->install_read);

            // Old meter: removal_read - previous_reading (last billed)
            $oldMeterConsumption = max(0, (float) $this->removal_read - (float) $this->previous_reading);

            return $newMeterConsumption + $oldMeterConsumption;
        }

        return max(0, (float) $this->present_reading - (float) $this->previous_reading);
    }

    /**
     * Get the full URL for the photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return asset($this->photo_path);
    }

    /**
     * Check if this reading has a photo.
     */
    public function getHasPhotoAttribute(): bool
    {
        return ! empty($this->photo_path);
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

    /**
     * Scope for processed readings.
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope for unprocessed readings.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Check if reading can be processed.
     */
    public function canBeProcessed(): bool
    {
        $baseRequirements = ! $this->is_processed
            && $this->present_reading !== null
            && $this->previous_reading !== null
            && $this->connection_id !== null;

        if (! $baseRequirements) {
            return false;
        }

        // For meter change, also require install_read and removal_read
        if ($this->is_meter_change) {
            return $this->install_read !== null && $this->removal_read !== null;
        }

        return true;
    }

    /**
     * Check if meter change data is complete.
     * Returns true if not a meter change, or if both install_read and removal_read are set.
     */
    public function hasMeterChangeDataComplete(): bool
    {
        if (! $this->is_meter_change) {
            return true;
        }

        return $this->install_read !== null && $this->removal_read !== null;
    }

    /**
     * Get missing meter change fields for validation messages.
     */
    public function getMissingMeterChangeFields(): array
    {
        if (! $this->is_meter_change) {
            return [];
        }

        $missing = [];
        if ($this->install_read === null) {
            $missing[] = 'install_read';
        }
        if ($this->removal_read === null) {
            $missing[] = 'removal_read';
        }

        return $missing;
    }
}
