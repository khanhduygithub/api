<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbFile = __DIR__ . '/database.json';
$db = json_decode(file_get_contents($dbFile), true);
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$key = $input['key'] ?? '';
$days = max(1, (int)($input['days'] ?? 30));

foreach ($db['keys'] as $i => $k) {
    if ($k['key'] === $key) {
        $db['keys'][$i]['expires_at'] = max($db['keys'][$i]['expires_at'], time()) + ($days * 86400);
        file_put_contents($dbFile, json_encode($db, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'message' => "Extended {$days} days!"]);
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Key not found']);
