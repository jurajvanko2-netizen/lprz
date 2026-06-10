<?php
/**
 * LIPOREZ — Image cache / resize
 * Zmenší obrázok na max 1400px šírky a uloží do img/cache/
 * Použitie: img_cache.php?src=img/nabytok/nabytok1.jpg
 */

$MAX_WIDTH  = 1400;
$MAX_HEIGHT = 1400;
$QUALITY    = 85;
$CACHE_DIR  = __DIR__ . '/img/cache/';

// --- Validácia vstupu ---
$src = $_GET['src'] ?? '';
// Odstráň traversal znaky a leading slash
$src = ltrim(str_replace(['..', '\\'], '', $src), '/');

if (!$src || !preg_match('/\.(jpe?g|png|webp)$/i', $src)) {
    http_response_code(400); exit('Invalid file');
}

// Musí začínať na img/ (bezpečnostná kontrola bez realpath)
if (strpos($src, 'img/') !== 0) {
    http_response_code(403); exit('Forbidden');
}

$srcPath = __DIR__ . '/' . $src;
if (!file_exists($srcPath) || !is_file($srcPath)) {
    // Fallback — pošli placeholder (prázdna odpoveď s 404)
    http_response_code(404); exit('Not found');
}

// --- Cache priečinok ---
if (!is_dir($CACHE_DIR)) {
    @mkdir($CACHE_DIR, 0755, true);
}

$cacheFile = $CACHE_DIR . md5($src) . '.jpg';

// Servi cache ak existuje a je rovnako nová alebo novšia ako originál
if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($srcPath)) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000'); // 30 dní
    readfile($cacheFile);
    exit;
}

// --- Pokus o resize cez GD ---
$ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));

if (!function_exists('imagecreatefromjpeg')) {
    // GD nie je dostupné — servi originál
    serveOriginal($srcPath);
}

$src_img = match($ext) {
    'jpg', 'jpeg' => @imagecreatefromjpeg($srcPath),
    'png'         => @imagecreatefrompng($srcPath),
    'webp'        => @imagecreatefromwebp($srcPath),
    default       => false
};

if (!$src_img) {
    serveOriginal($srcPath);
}

$origW = imagesx($src_img);
$origH = imagesy($src_img);

// Vypočítaj nové rozmery (nikdy nezväčšujeme)
$ratio = min($MAX_WIDTH / $origW, $MAX_HEIGHT / $origH, 1.0);
$newW  = (int) round($origW * $ratio);
$newH  = (int) round($origH * $ratio);

$dst_img = imagecreatetruecolor($newW, $newH);
imagefill($dst_img, 0, 0, imagecolorallocate($dst_img, 255, 255, 255));
imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
imagedestroy($src_img);

// Ulož do cache (ak zápis zlyhá, pošleme priamo z pamäte)
$saved = imagejpeg($dst_img, $cacheFile, $QUALITY);
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=2592000');
if ($saved) {
    imagedestroy($dst_img);
    readfile($cacheFile);
} else {
    // Cache zápis zlyhal — pošli z pamäte
    imagejpeg($dst_img, null, $QUALITY);
    imagedestroy($dst_img);
}
exit;

// --- Helper ---
function serveOriginal(string $path): never {
    $mime = mime_content_type($path) ?: 'image/jpeg';
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    readfile($path);
    exit;
}
