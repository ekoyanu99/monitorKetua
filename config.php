<?php

date_default_timezone_set('Asia/Jakarta');

$services = [
    'laravel_app_1' => [
        'name' => 'Laravel App 1',
        'url' => 'http://server1.yourdomain.com',
        'host' => '192.168.1.10',
        'port' => 80,
        'db_host' => '192.168.1.11',
        'db_port' => 1433,
        'type' => 'web'
    ],
    'laravel_app_2' => [
        'name' => 'Laravel App 2',
        'url' => 'http://server2.yourdomain.com',
        'host' => '192.168.1.12',
        'port' => 80,
        'db_host' => '192.168.1.13',
        'db_port' => 1433,
        'type' => 'web'
    ],
    'native_php_app' => [
        'name' => 'Native PHP App',
        'url' => 'http://server3.yourdomain.com',
        'host' => '192.168.1.14',
        'port' => 80,
        'db_host' => '192.168.1.15',
        'db_port' => 1433,
        'type' => 'web'
    ],
    'desktop_app' => [
        'name' => 'Desktop Application',
        'host' => '192.168.1.16',
        'port' => 80,
        'db_host' => '192.168.1.17',
        'db_port' => 1433,
        'process_name' => 'yourapp.exe',
        'type' => 'desktop'
    ]
];
