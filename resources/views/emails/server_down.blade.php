<!DOCTYPE html>
<html>

<head>
    <title>Server Alert</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <h2 style="color: red;">Server Down Alert</h2>

    <p>Sistem mendeteksi kegagalan pada layanan berikut:</p>

    <ul>
        <li><strong>Service Name:</strong> {{ $monitorName }}</li>
        <li><strong>Component:</strong> {{ strtoupper($component) }}</li>
        <li><strong>Time:</strong> {{ date('Y-m-d H:i:s') }}</li>
        <li><strong>Error Message:</strong></li>
    </ul>

    <div style="background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; color: #721c24;">
        {{ $errorMessage }}
    </div>

    <p>Mohon segera dicek.</p>
</body>

</html>
