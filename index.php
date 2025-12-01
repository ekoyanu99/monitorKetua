<?php
require_once 'config.php';
require_once 'functions.php';

foreach ($services as $serviceId => $service) {
    if ($service['type'] === 'web') {
        $web_status = checkHttpStatus($service['url']);
        $server_status = checkServiceStatus($service['host'], $service['port']);
        $db_status = checkSqlServer($service['db_host'], $service['db_port']);

        saveStatusHistory($serviceId, 'web', $web_status['status'], $web_status['message']);
        saveStatusHistory($serviceId, 'server', $server_status['status'], $server_status['message']);
        saveStatusHistory($serviceId, 'database', $db_status['status'], $db_status['message']);
    } elseif ($service['type'] === 'desktop') {
        $server_status = checkServiceStatus($service['host'], 3389);
        $db_status = checkSqlServer($service['db_host'], $service['db_port']);

        saveStatusHistory($serviceId, 'server', $server_status['status'], $server_status['message']);
        saveStatusHistory($serviceId, 'database', $db_status['status'], $db_status['message']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Monitoring Dashboard</title>
    <!-- Tailwind CSS via CDN for quick modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-semibold text-gray-800">Server Monitoring Dashboard</h1>
            <div class="space-x-2">
                <a href="history.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">View Detailed History</a>
            </div>
        </div>

        <div class="services-grid grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $serviceId => $service): ?>
                <?php
                if ($service['type'] === 'web') {
                    $web_status = checkHttpStatus($service['url']);
                    $server_status = checkServiceStatus($service['host'], $service['port']);
                    $db_status = checkSqlServer($service['db_host'], $service['db_port']);
                } elseif ($service['type'] === 'desktop') {
                    $server_status = checkServiceStatus($service['host'], 3389);
                    $db_status = checkSqlServer($service['db_host'], $service['db_port']);
                }

                $uptimeStats = getUptimeStats($serviceId, 24);
                $recentEvents = getRecentEvents($serviceId, 5);
                ?>

                <?php
                // small helper classes for status badges
                $serverBadge = ($server_status['status'] ?? 'down') === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $dbBadge = ($db_status['status'] ?? 'down') === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $webBadge = ($web_status['status'] ?? 'down') === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                $cardBorder = $service['type'] === 'web' ? 'border-l-4 border-blue-500' : 'border-l-4 border-green-500';
                ?>

                <div class="service-card bg-white shadow-sm rounded p-4 <?php echo $cardBorder; ?>">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-medium text-gray-800"><?php echo $service['name']; ?></h3>
                            <div class="text-sm text-gray-500 capitalize"><?php echo $service['type']; ?></div>
                        </div>
                        <div class="text-right text-sm text-gray-400">Last: <?php echo date('Y-m-d H:i'); ?></div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-xs text-gray-500">Uptime</div>
                            <div class="text-lg font-semibold <?php echo $uptimeStats['uptime_percent'] > 95 ? 'text-green-600' : ($uptimeStats['uptime_percent'] > 90 ? 'text-yellow-600' : 'text-red-600'); ?>">
                                <?php echo $uptimeStats['uptime_percent']; ?>%
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-xs text-gray-500">Checks</div>
                            <div class="text-lg font-semibold"><?php echo $uptimeStats['total_checks']; ?></div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-xs text-gray-500">Downtimes</div>
                            <div class="text-lg font-semibold text-red-600"><?php echo $uptimeStats['downtime_count']; ?></div>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <?php if ($service['type'] === 'web'): ?>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">Web Service</div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo $webBadge; ?>"><?php echo strtoupper($web_status['status']); ?></span>
                                    <div class="text-xs text-gray-500"><?php echo $web_status['message']; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">Server</div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $serverBadge; ?>"><?php echo strtoupper($server_status['status']); ?></span>
                                <div class="text-xs text-gray-500"><?php echo $server_status['message']; ?></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">Database</div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $dbBadge; ?>"><?php echo strtoupper($db_status['status']); ?></span>
                                <div class="text-xs text-gray-500"><?php echo $db_status['message']; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700">Recent Events</h4>
                        <div class="mt-2 space-y-2">
                            <?php foreach ($recentEvents as $event): ?>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-xs text-gray-400"><?php echo date('H:i', strtotime($event['timestamp'])); ?></div>
                                        <div><?php echo ucfirst($event['component']); ?></div>
                                    </div>
                                    <div class="text-xs <?php echo $event['status'] === 'up' ? 'text-green-600' : 'text-red-600'; ?> uppercase"><?php echo strtoupper($event['status']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 flex items-center justify-between text-sm text-gray-500">
            <div>Last updated: <?php echo date('Y-m-d H:i:s'); ?></div>
            <div class="space-x-2">
                <button onclick="location.reload()" class="px-3 py-1 bg-gray-100 rounded">Refresh</button>
                <button onclick="autoRefresh()" id="auto-refresh" class="px-3 py-1 bg-gray-100 rounded">Auto Refresh (30s)</button>
            </div>
        </div>
    </div>

    <script>
        let autoRefreshInterval;

        function autoRefresh() {
            const button = document.getElementById('auto-refresh');
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                button.textContent = 'Auto Refresh (30s)';
            } else {
                autoRefreshInterval = setInterval(() => {
                    location.reload();
                }, 30000);
                button.textContent = 'Stop Auto Refresh';
            }
        }
    </script>
</body>

</html>