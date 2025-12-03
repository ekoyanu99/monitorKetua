<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Monitor;
use App\Models\MonitorHistory;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServerDownMail;

class RunSystemMonitor extends Command
{
    protected $signature = 'monitor:run';
    protected $description = 'Check health status of all registered services';

    public function handle()
    {
        $monitors = Monitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            $this->info("Checking {$monitor->name}...");

            if ($monitor->type === 'web' && $monitor->url) {
                $this->checkWeb($monitor);
            }

            if ($monitor->host) {
                $port = $monitor->type === 'desktop' ? 3389 : ($monitor->port ?? 80);
                $this->checkPort($monitor, 'server', $monitor->host, $port);
            }

            if ($monitor->db_host) {
                $this->checkPort($monitor, 'database', $monitor->db_host, $monitor->db_port);
            }
        }

        $this->info('All checks completed.');
    }

    private function checkWeb($monitor)
    {
        $start = microtime(true);
        try {
            $response = Http::timeout(5)->withoutVerifying()->get($monitor->url);
            $status = $response->successful() ? 'up' : 'down';
            $msg = "HTTP " . $response->status();
        } catch (\Exception $e) {
            $status = 'down';
            $msg = $e->getMessage();
        }
        $latency = round((microtime(true) - $start) * 1000);

        $this->saveHistory($monitor->id, 'web', $status, $msg, $latency);
    }

    private function checkPort($monitor, $component, $host, $port)
    {
        $start = microtime(true);
        $connected = @fsockopen($host, $port, $errno, $errstr, 2);

        $latency = round((microtime(true) - $start) * 1000);

        if ($connected) {
            fclose($connected);
            $status = 'up';
            $msg = "Port $port Open";
        } else {
            $status = 'down';
            $msg = "Connection Refused/Timeout ($errno)";
        }

        $this->saveHistory($monitor->id, $component, $status, $msg, $latency);
    }

    private function saveHistory($monitorId, $component, $status, $message, $latency)
    {
        $lastHistory = MonitorHistory::where('monitor_id', $monitorId)
            ->where('component', $component)
            ->latest()
            ->first();

        $previousStatus = $lastHistory ? $lastHistory->status : 'up';

        MonitorHistory::create([
            'monitor_id' => $monitorId,
            'component'  => $component,
            'status'     => $status,
            'message'    => substr($message, 0, 250),
            'latency'    => $latency
        ]);

        if ($status === 'down' && $previousStatus === 'up') {

            $monitor = Monitor::find($monitorId);
            $monitorName = $monitor ? $monitor->name : 'Unknown Service';

            Mail::to('it.cosmolashes@gmail.com')->send(
                new ServerDownMail($monitorName, $component, $message)
            );

            echo "Email alert sent for {$monitorName}!\n";
        }
    }
}
