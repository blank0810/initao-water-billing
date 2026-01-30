<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'account_type';

    protected $primaryKey = 'at_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'at_desc',
        'stat_id',
    ];

    /**
     * Get the status associated with the account type
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the service connections for the account type
     */
    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'account_type_id', 'at_id');
    }
}
