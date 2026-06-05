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
$categories = CATEGORIES;

if (!array_key_exists($category, $categories)) {
    die(json_encode(['error' => 'Neplatná kategória.']));
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['image']['error'] ?? 'žiadny súbor';
    die(json_encode(['error' => 'Chyba nahrávania: ' . $err]));
}

$file = $_FILES['image'];
$tmpPath = $file['tmp_name'];
$origName = $file['name'];
$mimeType = mime_content_type($tmpPath);

if (!in_array($mimeType, ALLOWED_TYPES)) {
    die(json_encode(['error' => 'Nepodporovaný formát súboru. Povolené: JPG, PNG, WebP.']));
}

// Generuj bezpečný názov súboru
$ext = 'jpg'; // vždy ukladáme ako JPG
$safeName = preg_replace('/[^a-z0-9_-]/', '', strtolower(pathinfo($origName, PATHINFO_FILENAME)));
if (empty($safeName)) $safeName = 'foto';
$safeName = substr($safeName, 0, 60);

$destDir = IMG_ROOT . $category . '/';
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

// Vyrieš konflikty v názvoch
$finalName = $safeName . '.' . $ext;
$counter = 1;
while (file_exists($destDir . $finalName)) {
    $finalName = $safeName . '_' . $counter . '.' . $ext;
    $counter++;
}
$destPath = $destDir . $finalName;

// Načítaj obrázok
$srcImage = null;
switch ($mimeType) {
    case 'image/jpeg':
    case 'image/jpg':
        $srcImage = @imagecreatefromjpeg($tmpPath);
        break;
    case 'image/png':
        $srcImage = @imagecreatefrompng($tmpPath);
        break;
    case 'image/webp':
        $srcImage = @imagecreatefromwebp($tmpPath);
        break;
}

if (!$srcImage) {
    die(json_encode(['error' => 'Nepodarilo sa načítať obrázok. Skontrolujte formát súboru.']));
}

$srcW = imagesx($srcImage);
$srcH = imagesy($srcImage);

// Vypočítaj nové rozmery
$maxDim = MAX_DIMENSION;
if ($srcW > $maxDim || $srcH > $maxDim) {
    if ($srcW >= $srcH) {
        $newW = $maxDim;
        $newH = (int)round($srcH * ($maxDim / $srcW));
    } else {
        $newH = $maxDim;
        $newW = (int)round($srcW * ($maxDim / $srcH));
    }
} else {
    $newW = $srcW;
    $newH = $srcH;
}

// Vytvor nový obrázok
$dstImage = imagecreatetruecolor($newW, $newH);

// Zachovaj biely pozadie pre PNG (bez priehľadnosti)
$white = imagecolorallocate($dstImage, 255, 255, 255);
imagefill($dstImage, 0, 0, $white);

imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
imagedestroy($srcImage);

// Nájdi správnu kvalitu JPEG tak, aby výsledok bol ≤ 1MB
$quality = 85;
$maxBytes = MAX_OUTPUT_SIZE;

while ($quality >= 40) {
    ob_start();
    imagejpeg($dstImage, null, $quality);
    $data = ob_get_clean();
    if (strlen($data) <= $maxBytes) break;
    $quality -= 5;
}

// Ak je stále príliš veľký, zmenši rozmery
if (strlen($data) > $maxBytes) {
    $scale = 0.8;
    while (strlen($data) > $maxBytes && $newW > 400) {
        $newW2 = (int)($newW * $scale);
        $newH2 = (int)($newH * $scale);
        $dstImage2 = imagecreatetruecolor($newW2, $newH2);
        $white2 = imagecolorallocate($dstImage2, 255, 255, 255);
        imagefill($dstImage2, 0, 0, $white2);
        imagecopyresampled($dstImage2, $dstImage, 0, 0, 0, 0, $newW2, $newH2, $newW, $newH);
        imagedestroy($dstImage);
        $dstImage = $dstImage2;
        $newW = $newW2;
        $newH = $newH2;
        ob_start();
        imagejpeg($dstImage, null, 75);
        $data = ob_get_clean();
    }
}

imagedestroy($dstImage);

// Ulož súbor
if (file_put_contents($destPath, $data) === false) {
    die(json_encode(['error' => 'Nepodarilo sa uložiť súbor. Skontrolujte oprávnenia priečinka.']));
}

$finalSize = strlen($data);
$relativePath = 'img/' . $category . '/' . $finalName;

echo json_encode([
    'success' => true,
    'filename' => $finalName,
    'path' => $relativePath,
    'size_kb' => round($finalSize / 1024),
    'dimensions' => $newW . 'x' . $newH,
    'quality' => $quality,
]);
