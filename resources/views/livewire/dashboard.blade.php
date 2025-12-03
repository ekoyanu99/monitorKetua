<div wire:poll.30s class="container min-h-screen p-6 mx-auto bg-gray-100">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Server Monitoring Dashboard</h1>
        <div class="flex items-center space-x-2">
            <span wire:loading class="mr-2 text-xs text-blue-500">
                Updating...
            </span>

            <a href="{{ route('monitors') }}"
                class="inline-block px-4 py-2 text-gray-700 bg-white border rounded shadow hover:bg-gray-50">
                Manage Monitors
            </a>

            <a href="{{ route('history') }}"
                class="inline-block px-4 py-2 text-white bg-blue-600 rounded shadow hover:bg-blue-700">
                View History
            </a>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($monitors as $monitor)
            @php
                $webStatus = $monitor->lastStatus('web');
                $serverStatus = $monitor->lastStatus('server');
                $dbStatus = $monitor->lastStatus('database');
                $stats = $monitor->getUptimeStats();
                $recentEvents = $monitor->recentEvents();
                $cardBorder = $monitor->type === 'web' ? 'border-l-4 border-blue-500' : 'border-l-4 border-green-500';
            @endphp

            <div class="bg-white shadow-sm rounded p-4 {{ $cardBorder }}">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-medium text-gray-800">{{ $monitor->name }}</h3>
                        <div class="text-sm text-gray-500 capitalize">{{ $monitor->type }}</div>
                    </div>
                    <div class="text-sm text-right text-gray-400">
                        {{ $serverStatus ? $serverStatus->created_at->diffForHumans() : '-' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-4">
                    <div class="p-3 rounded bg-gray-50">
                        <div class="text-xs text-gray-500">Uptime (24h)</div>
                        <div
                            class="text-lg font-semibold {{ $stats['percent'] > 98 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stats['percent'] }}%
                        </div>
                    </div>
                    <div class="p-3 rounded bg-gray-50">
                        <div class="text-xs text-gray-500">Checks</div>
                        <div class="text-lg font-semibold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="p-3 rounded bg-gray-50">
                        <div class="text-xs text-gray-500">Downtimes</div>
                        <div class="text-lg font-semibold text-red-600">{{ $stats['downtime'] }}</div>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @if ($monitor->type === 'web')
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">Web Service</div>
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ optional($webStatus)->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ strtoupper(optional($webStatus)->status ?? 'N/A') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">Server</div>
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ optional($serverStatus)->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ strtoupper(optional($serverStatus)->status ?? 'N/A') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">Database</div>
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ optional($dbStatus)->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ strtoupper(optional($dbStatus)->status ?? 'N/A') }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- <div class="mt-6 text-xs text-center text-gray-400">
        Auto-refreshing every 30 seconds...
    </div> --}}
</div>
