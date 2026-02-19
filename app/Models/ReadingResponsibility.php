<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingResponsibility extends Model
{
    protected $table = 'view_reading_responsibilities';

    public $timestamps = false;

    protected $primaryKey = 'reading_id';

    public $incrementing = false;

    protected $fillable = [];
}
