<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$openId = 'OPEN-' . strtoupper(bin2hex(random_bytes(12)));
echo json_encode(['open_id' => $openId, 'url' => 'https://khanhduyapi.free.nf/']);
