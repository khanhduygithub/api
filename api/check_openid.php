<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$openId = $input['open_id'] ?? '';
if (empty($openId)) {
    echo json_encode(['status' => 'new', 'url' => 'https://khanhduyapi.free.nf/']);
} else {
    echo json_encode(['status' => 'exists', 'udid' => 'DEVICE-' . strtoupper(substr(md5($openId), 0, 16))]);
}
