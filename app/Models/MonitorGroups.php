<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorGroups extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'series',
        'queue_number',
    ];

    public $timestamps = false;

}
