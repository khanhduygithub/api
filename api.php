<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbFile = __DIR__ . '/api/database.json';

function readDB() {
    global $dbFile;
    if (!file_exists($dbFile)) {
        $initial = ['keys' => [], 'offsets' => [], 'settings' => ['site_title' => 'Monite API', 'maintenance_mode' => false]];
        file_put_contents($dbFile, json_encode($initial, JSON_PRETTY_PRINT));
        return $initial;
    }
    return json_decode(file_get_contents($dbFile), true) ?: ['keys' => [], 'offsets' => []];
}

function writeDB($data) {
    global $dbFile;
    file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?: [];

switch ($action) {
    case 'generate_key':
        $versionName = $input['version_name'] ?? 'OB53-Free';
        $expiryDays = max(1, (int)($input['expiry_days'] ?? 30));
        $random = bin2hex(random_bytes(12));
        $key = 'MONITE-' . strtoupper(substr($random, 0, 8) . '-' . substr($random, 8, 8) . '-' . substr($random, 16, 8));
        $expiresAt = time() + ($expiryDays * 86400);
        
        $db = readDB();
        $db['keys'][] = ['key' => $key, 'created_at' => time(), 'expires_at' => $expiresAt, 'version_name' => $versionName, 'active' => true, 'usage_count' => 0, 'last_used' => null];
        writeDB($db);
        echo json_encode(['success' => true, 'key' => $key, 'message' => 'Key created!']);
        break;
        
    case 'list_keys':
        $db = readDB();
        echo json_encode(['success' => true, 'keys' => $db['keys']]);
        break;
        
    case 'delete_key':
        $key = $input['key'] ?? '';
        $db = readDB();
        foreach ($db['keys'] as $i => $k) {
            if ($k['key'] === $key) { unset($db['keys'][$i]); $db['keys'] = array_values($db['keys']); writeDB($db); echo json_encode(['success' => true, 'message' => 'Key deleted!']); exit; }
        }
        echo json_encode(['success' => false, 'message' => 'Key not found']);
        break;
        
    case 'extend_key':
        $key = $input['key'] ?? '';
        $days = max(1, (int)($input['days'] ?? 30));
        $db = readDB();
        foreach ($db['keys'] as $i => $k) {
            if ($k['key'] === $key) { $db['keys'][$i]['expires_at'] = max($db['keys'][$i]['expires_at'], time()) + ($days * 86400); writeDB($db); echo json_encode(['success' => true, 'message' => "Extended {$days} days!"]); exit; }
        }
        echo json_encode(['success' => false, 'message' => 'Key not found']);
        break;
        
    case 'save_offsets':
        $offsets = $input['offsets'] ?? [];
        $db = readDB();
        $db['offsets'] = $offsets;
        writeDB($db);
        echo json_encode(['success' => true, 'message' => 'Offsets saved!']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
