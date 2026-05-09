<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbFile = __DIR__ . '/database.json';
$db = json_decode(file_get_contents($dbFile), true);
$input = json_decode(file_get_contents('php://input'), true) ?: [];

$versionName = $input['version_name'] ?? 'OB53-Free';
$expiryDays = max(1, (int)($input['expiry_days'] ?? 30));
$random = bin2hex(random_bytes(12));
$key = 'MONITE-' . strtoupper(substr($random, 0, 8) . '-' . substr($random, 8, 8) . '-' . substr($random, 16, 8));

$db['keys'][] = [
    'key' => $key,
    'created_at' => time(),
    'expires_at' => time() + ($expiryDays * 86400),
    'version_name' => $versionName,
    'active' => true,
    'usage_count' => 0,
    'last_used' => null
];
file_put_contents($dbFile, json_encode($db, JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'key' => $key, 'message' => 'Key created!']);
