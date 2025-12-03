<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Monitor;
use App\Models\MonitorHistory;
use Carbon\Carbon;

class History extends Component
{
    use WithPagination;

    public $serviceId;
    public $timeRange = '24h';

    public function mount()
    {
        $firstMonitor = Monitor::where('is_active', true)->first();
        $this->serviceId = $firstMonitor ? $firstMonitor->id : null;
    }
    public function updatedServiceId()
    {
        $this->resetPage();
    }
    public function updatedTimeRange()
    {
        $this->resetPage();
    }

    public function render()
    {
        $monitors = Monitor::where('is_active', true)->get();

        $hoursMap = ['1h' => 1, '24h' => 24, '7d' => 168, '30d' => 720];
        $hours = $hoursMap[$this->timeRange] ?? 24;
        $since = Carbon::now()->subHours($hours);

        $queryBase = MonitorHistory::where('monitor_id', $this->serviceId)
            ->where('created_at', '>=', $since);

        $totalChecks = $queryBase->count();
        $downtimeCount = (clone $queryBase)->where('status', 'down')->count();
        $uptimeCount = $totalChecks - $downtimeCount;

        $uptimePercent = $totalChecks > 0
            ? round(($uptimeCount / $totalChecks) * 100, 2)
            : 100;

        $history = MonitorHistory::where('monitor_id', $this->serviceId)
            ->where('created_at', '>=', $since)
            ->latest()
            ->paginate(50);

        return view('livewire.history', [
            'monitors' => $monitors,
            'history' => $history,
            'stats' => [
                'uptime_percent' => $uptimePercent,
                'total_checks' => $totalChecks,
                'up_count' => $uptimeCount,
                'down_count' => $downtimeCount,
            ]
        ]);
    }
}
