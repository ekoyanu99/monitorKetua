<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Monitor extends Model
{
    protected $table = 'monitors';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'type',
        'url',
        'host',
        'port',
        'db_host',
        'db_port',
        'is_active'
    ];

    protected $guarded = [];

    public function histories()
    {
        return $this->hasMany(MonitorHistory::class);
    }

    public function lastStatus($component)
    {
        return $this->histories()
            ->where('component', $component)
            ->latest()
            ->first();
    }

    public function recentEvents()
    {
        return $this->histories()
            ->latest()
            ->take(5)
            ->get();
    }

    public function getUptimeStats()
    {
        $since = Carbon::now()->subHours(24);

        $logs = $this->histories()
            ->where('created_at', '>=', $since)
            ->get();

        $totalChecks = $logs->count();
        if ($totalChecks === 0) {
            return [
                'percent' => 100,
                'total' => 0,
                'downtime' => 0
            ];
        }

        $downtimeCount = $logs->where('status', 'down')->count();

        $uptimePercent = round((($totalChecks - $downtimeCount) / $totalChecks) * 100, 2);

        return [
            'percent' => $uptimePercent,
            'total' => $totalChecks,
            'downtime' => $downtimeCount
        ];
    }
}
