<?php
// ------------------------------
// Load .env manually
// ------------------------------
$env = [];
if (file_exists('.env')) {
    $lines = file('.env');
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $env[$parts[0]] = $parts[1];
        }
    }
}

$apiUrl = $env['API_URL'] ?? 'http://localhost:3000/protected';
$apiToken = $env['API_TOKEN'] ?? '';

$response = '';
$httpCode = '';
$curlError = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Prepare payload
    $payload = json_encode([
        "name" => htmlspecialchars($_POST["name"]),
        "email" => htmlspecialchars($_POST["email"]),
        "message" => htmlspecialchars($_POST["message"])
    ]);

    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiToken"
    ]);

    // Execute request
    $response = curl_exec($ch);
    if ($response === false) {
        $curlError = curl_error($ch);
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP → Express API Form</title>
<style>
    body { font-family: Arial; background: #f9f9f9; display: flex; justify-content: center; padding-top: 50px; }
    .container { background: white; padding: 25px; border-radius: 10px; width: 400px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    h2 { margin-top: 0; }
    input, textarea { width: 100%; padding: 10px; margin-top: 6px; margin-bottom: 14px; border: 1px solid #ccc; border-radius: 6px; }
    button { background-color: #007bff; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; }
    button:hover { background-color: #0056b3; }
    .output { margin-top: 20px; background: #f0f8ff; border: 1px solid #d0e7ff; border-radius: 8px; padding: 12px; }
    pre { white-space: pre-wrap; word-wrap: break-word; }
</style>
</head>
<body>
<div class="container">
    <h2>Send Form Data to API</h2>
    <form method="POST" action="">
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Message:</label>
        <textarea name="message" rows="4" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <?php if (!empty($curlError)): ?>
        <div class="output">
            <h3>cURL Error:</h3>
            <pre><?= htmlspecialchars($curlError) ?></pre>
        </div>
    <?php elseif (!empty($response)): ?>
        <div class="output" id="result">
            <h3>API Response (HTTP <?= htmlspecialchars($httpCode) ?>):</h3>
            <pre><?= htmlspecialchars($response) ?></pre>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
