<?php
function checkServiceStatus($host, $port, $timeout = 5)
{
    try {
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($socket) {
            fclose($socket);
            return ['status' => 'up', 'message' => 'Online'];
        }
        return ['status' => 'down', 'message' => $errstr];
    } catch (Exception $e) {
        return ['status' => 'down', 'message' => $e->getMessage()];
    }
}

function checkHttpStatus($url, $timeout = 5)
{
    try {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode >= 200 && $httpCode < 400
                ? ['status' => 'up', 'message' => "HTTP $httpCode"]
                : ['status' => 'down', 'message' => "HTTP $httpCode"];
        }

        $headers = @get_headers($url, 1);
        if ($headers && isset($headers[0])) {
            if (preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $headers[0], $m)) {
                $code = (int)$m[1];
                return $code >= 200 && $code < 400
                    ? ['status' => 'up', 'message' => "HTTP $code"]
                    : ['status' => 'down', 'message' => "HTTP $code"];
            }
        }

        $parsed = parse_url($url);
        $host = $parsed['host'] ?? $url;
        $port = (isset($parsed['scheme']) && $parsed['scheme'] === 'https') ? 443 : 80;
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($socket) {
            fclose($socket);
            return ['status' => 'up', 'message' => 'Online'];
        }

        return ['status' => 'down', 'message' => $errstr ?: 'Unable to reach host'];
    } catch (Exception $e) {
        return ['status' => 'down', 'message' => $e->getMessage()];
    }
}

function checkSqlServer($host, $port, $timeout = 5)
{
    return checkServiceStatus($host, $port, $timeout);
}

function saveStatusHistory($serviceId, $component, $status, $message)
{
    $historyFile = __DIR__ . '/data/history.json';
    $timestamp = date('Y-m-d H:i:s');

    if (!is_dir(dirname($historyFile))) {
        mkdir(dirname($historyFile), 0755, true);
    }

    $history = [];
    if (file_exists($historyFile)) {
        $history = json_decode(file_get_contents($historyFile), true) ?: [];
    }

    if (!isset($history[$serviceId])) {
        $history[$serviceId] = [];
    }

    $history[$serviceId][] = [
        'timestamp' => $timestamp,
        'component' => $component,
        'status' => $status,
        'message' => $message
    ];

    if (count($history[$serviceId]) > 1000) {
        $history[$serviceId] = array_slice($history[$serviceId], -1000);
    }

    file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));
}

function getUptimeStats($serviceId, $hours = 24)
{
    $historyFile = __DIR__ . '/data/history.json';
    if (!file_exists($historyFile)) {
        return ['uptime_percent' => 0, 'total_checks' => 0, 'downtime_count' => 0];
    }

    $history = json_decode(file_get_contents($historyFile), true);
    $cutoffTime = date('Y-m-d H:i:s', strtotime("-$hours hours"));

    if (!isset($history[$serviceId])) {
        return ['uptime_percent' => 0, 'total_checks' => 0, 'downtime_count' => 0];
    }

    $serviceHistory = array_filter($history[$serviceId], function ($entry) use ($cutoffTime) {
        return $entry['timestamp'] >= $cutoffTime;
    });

    $totalChecks = count($serviceHistory);
    $upCount = 0;
    $downtimeCount = 0;

    foreach ($serviceHistory as $entry) {
        if ($entry['status'] === 'up') {
            $upCount++;
        } else {
            $downtimeCount++;
        }
    }

    $uptimePercent = $totalChecks > 0 ? round(($upCount / $totalChecks) * 100, 2) : 0;

    return [
        'uptime_percent' => $uptimePercent,
        'total_checks' => $totalChecks,
        'downtime_count' => $downtimeCount,
        'last_24h_up' => $upCount,
        'last_24h_down' => $downtimeCount
    ];
}

function getRecentEvents($serviceId, $limit = 10)
{
    $historyFile = __DIR__ . '/data/history.json';
    if (!file_exists($historyFile)) {
        return [];
    }

    $history = json_decode(file_get_contents($historyFile), true);

    if (!isset($history[$serviceId])) {
        return [];
    }

    $serviceHistory = $history[$serviceId];
    usort($serviceHistory, function ($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    return array_slice($serviceHistory, 0, $limit);
}
