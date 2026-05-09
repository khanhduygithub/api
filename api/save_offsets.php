<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbFile = __DIR__ . '/database.json';
$db = json_decode(file_get_contents($dbFile), true);
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$offsets = $input['offsets'] ?? [];

if (empty($offsets)) {
    echo json_encode(['success' => false, 'message' => 'No offsets']);
    exit;
}
$db['offsets'] = $offsets;
file_put_contents($dbFile, json_encode($db, JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'message' => 'Offsets saved!']);
