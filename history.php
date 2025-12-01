<?php
require_once 'config.php';
require_once 'functions.php';

$serviceId = $_GET['service'] ?? array_key_first($services);
$timeRange = $_GET['range'] ?? '24h';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service History</title>
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = window.tailwind.config || {};
        window.tailwind.config.darkMode = 'class';
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container mx-auto p-6 bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Service History</h1>
            <div class="flex items-center space-x-3">
                <a href="index.php" class="inline-block bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded">Back to Dashboard</a>
                <button id="theme-toggle" aria-label="Toggle theme" class="ml-3 inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded">
                    <span id="theme-toggle-icon">🌙</span>
                </button>
            </div>
        </div>

        <div class="filters mb-4">
            <form method="GET" class="flex items-center space-x-4">
                <label class="text-sm text-gray-600">Service:</label>
                <select name="service" onchange="this.form.submit()" class="border rounded px-2 py-1">
                    <?php foreach ($services as $id => $service): ?>
                        <option value="<?php echo $id; ?>" <?php echo $id === $serviceId ? 'selected' : ''; ?>>
                            <?php echo $service['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="text-sm text-gray-600">Time Range:</label>
                <select name="range" onchange="this.form.submit()" class="border rounded px-2 py-1">
                    <option value="1h" <?php echo $timeRange === '1h' ? 'selected' : ''; ?>>Last 1 Hour</option>
                    <option value="24h" <?php echo $timeRange === '24h' ? 'selected' : ''; ?>>Last 24 Hours</option>
                    <option value="7d" <?php echo $timeRange === '7d' ? 'selected' : ''; ?>>Last 7 Days</option>
                    <option value="30d" <?php echo $timeRange === '30d' ? 'selected' : ''; ?>>Last 30 Days</option>
                </select>
            </form>
        </div>

        <?php
        $hoursMap = ['1h' => 1, '24h' => 24, '7d' => 168, '30d' => 720];
        $hours = $hoursMap[$timeRange] ?? 24;
        $stats = getUptimeStats($serviceId, $hours);
        $history = getRecentEvents($serviceId, 1000);
        ?>

        <div class="stats-overview mb-6">
            <h2 class="text-lg font-medium text-gray-800">Uptime Statistics (<?php echo $timeRange; ?>)</h2>
            <div class="mt-3 grid grid-cols-4 gap-3">
                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                    <div class="text-xs text-gray-500">Uptime Percentage</div>
                    <div class="text-2xl font-semibold <?php echo $stats['uptime_percent'] > 95 ? 'text-green-600' : ($stats['uptime_percent'] > 90 ? 'text-yellow-600' : 'text-red-600'); ?>">
                        <?php echo $stats['uptime_percent']; ?>%
                    </div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                    <div class="text-xs text-gray-500">Total Checks</div>
                    <div class="text-2xl font-semibold"><?php echo $stats['total_checks']; ?></div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                    <div class="text-xs text-gray-500">Successful</div>
                    <div class="text-2xl font-semibold text-green-600"><?php echo $stats['last_24h_up']; ?></div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                    <div class="text-xs text-gray-500">Failures</div>
                    <div class="text-2xl font-semibold text-red-600"><?php echo $stats['last_24h_down']; ?></div>
                </div>
            </div>
        </div>

        <div class="history-table bg-white dark:bg-gray-800 rounded shadow-sm p-4">
            <h2 class="text-lg font-medium text-gray-800 mb-3">Detailed History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Timestamp</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Component</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Message</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($history as $event): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-600"><?php echo $event['timestamp']; ?></td>
                                <td class="px-4 py-2 text-sm text-gray-600"><?php echo ucfirst($event['component']); ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs <?php echo $event['status'] === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo strtoupper($event['status']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600"><?php echo $event['message']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>