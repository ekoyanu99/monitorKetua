<div class="container min-h-screen p-6 mx-auto text-gray-900 bg-gray-50">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Service History</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}"
                class="inline-block px-3 py-2 transition bg-white border rounded hover:bg-gray-100">
                &larr; Back to Dashboard
            </a>
        </div>
    </div>

    <div class="p-4 mb-6 bg-white rounded shadow-sm">
        <div class="flex items-center space-x-4">
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Service</label>
                <select wire:model.live="serviceId"
                    class="w-64 px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($monitors as $monitor)
                        <option value="{{ $monitor->id }}">{{ $monitor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500 uppercase">Time Range</label>
                <select wire:model.live="timeRange"
                    class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="1h">Last 1 Hour</option>
                    <option value="24h">Last 24 Hours</option>
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                </select>
            </div>

            <div wire:loading class="pt-5 text-sm text-blue-500">
                Updating data...
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="mb-3 text-lg font-medium text-gray-800">Statistics ({{ $timeRange }})</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div
                class="p-4 bg-white rounded shadow-sm border-l-4 {{ $stats['uptime_percent'] > 98 ? 'border-green-500' : 'border-red-500' }}">
                <div class="text-xs text-gray-500 uppercase">Uptime Percentage</div>
                <div class="text-2xl font-bold {{ $stats['uptime_percent'] > 95 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stats['uptime_percent'] }}%
                </div>
            </div>
            <div class="p-4 bg-white rounded shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Total Checks</div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_checks']) }}</div>
            </div>
            <div class="p-4 bg-white rounded shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Successful</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($stats['up_count']) }}</div>
            </div>
            <div class="p-4 bg-white rounded shadow-sm">
                <div class="text-xs text-gray-500 uppercase">Failures</div>
                <div class="text-2xl font-bold text-red-600">{{ number_format($stats['down_count']) }}</div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden bg-white rounded shadow-sm">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-medium text-gray-800">Detailed Log</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Timestamp</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Component</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Latency</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Message</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($history as $event)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $event->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 capitalize whitespace-nowrap">
                                {{ $event->component }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $event->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ strtoupper($event->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $event->latency }}ms
                            </td>
                            <td class="max-w-xs px-6 py-4 text-sm text-gray-500 truncate"
                                title="{{ $event->message }}">
                                {{ $event->message }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No history found for this
                                period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $history->links() }}
        </div>
    </div>
</div>
