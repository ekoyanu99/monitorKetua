<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorHistory extends Model
{
    protected $table = 'monitor_histories';
    public $timestamps = true;
    protected $fillable = [
        'monitor_id',
        'component',
        'status',
        'latency',
        'message',
    ];
}
