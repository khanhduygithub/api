<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$db = json_decode(file_get_contents(__DIR__ . '/database.json'), true);
echo json_encode(['success' => true, 'offsets' => $db['offsets']]);
