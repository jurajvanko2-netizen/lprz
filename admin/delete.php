<?php
require_once 'config.php';
session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    die(json_encode(['error' => 'Nie ste prihlásený.']));
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['error' => 'Neplatná metóda.']));
}

$category = $_POST['category'] ?? '';
$filename = $_POST['filename'] ?? '';

if (!array_key_exists($category, CATEGORIES)) {
    die(json_encode(['error' => 'Neplatná kategória.']));
}

// Bezpečnostná kontrola — len základný názov súboru, žiadne cesty
$filename = basename($filename);
if (empty($filename)) {
    die(json_encode(['error' => 'Neplatný názov súboru.']));
}

// Povolené prípony
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    die(json_encode(['error' => 'Neplatná prípona súboru.']));
}

$filePath = IMG_ROOT . $category . '/' . $filename;

if (!file_exists($filePath)) {
    die(json_encode(['error' => 'Súbor neexistuje.']));
}

if (unlink($filePath)) {
    echo json_encode(['success' => true, 'deleted' => $filename]);
} else {
    die(json_encode(['error' => 'Nepodarilo sa vymazať súbor.']));
}
