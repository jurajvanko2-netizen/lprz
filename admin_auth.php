<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['ok'=>false]); exit;
}

require_once __DIR__ . '/password_config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !verifyAdminPassword($data['password'] ?? '')) {
    http_response_code(403); echo json_encode(['ok'=>false]); exit;
}

echo json_encode(['ok'=>true]);
