<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerSource extends Model
{
    protected $table = 'ledger_sources';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'table_name'
    ];

    public function ledgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'source_type_id');
    }
}
