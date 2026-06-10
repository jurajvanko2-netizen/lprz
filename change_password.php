<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['ok'=>false]); exit;
}

require_once __DIR__ . '/password_config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !verifyAdminPassword($data['oldPassword'] ?? '')) {
    http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Nesprávne staré heslo']); exit;
}

$newPwd = $data['newPassword'] ?? '';
if (strlen($newPwd) < 6) {
    echo json_encode(['ok'=>false,'error'=>'Nové heslo musí mať aspoň 6 znakov']); exit;
}

if (!setAdminPassword($newPwd)) {
    echo json_encode(['ok'=>false,'error'=>'Nepodarilo sa uložiť heslo (oprávnenia?)']); exit;
}

echo json_encode(['ok'=>true]);
