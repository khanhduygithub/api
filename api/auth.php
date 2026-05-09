<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbFile = __DIR__ . '/database.json';
$db = json_decode(file_get_contents($dbFile), true);
$input = json_decode(file_get_contents('php://input'), true) ?: [];

$key = $input['key'] ?? '';
if (empty($key)) {
    echo json_encode(['success' => false, 'message' => 'Key is required']);
    exit;
}

foreach ($db['keys'] as $i => $k) {
    if ($k['key'] === $key && $k['active'] && $k['expires_at'] > time()) {
        $db['keys'][$i]['last_used'] = time();
        $db['keys'][$i]['usage_count'] = ($db['keys'][$i]['usage_count'] ?? 0) + 1;
        file_put_contents($dbFile, json_encode($db, JSON_PRETTY_PRINT));
        echo json_encode([
            'success' => true,
            'key' => $k['key'],
            'version_name' => $k['version_name'],
            'version' => $k['created_at'],
            'expires_at' => $k['expires_at'],
            'message' => 'Login successful'
        ]);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid or expired key']);
