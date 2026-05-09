<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$db = json_decode(file_get_contents(__DIR__ . '/database.json'), true);
$maintenance = $db['settings']['maintenance_mode'] ?? false;
echo json_encode(['success' => true, 'allowed' => !$maintenance]);
