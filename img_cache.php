<?php
/**
 * LIPOREZ — Image cache / resize
 * Zmenší obrázok na max 1400px šírky a uloží do img/cache/
 * Použitie: img_cache.php?src=img/nabytok/nabytok1.jpg
 */

$MAX_WIDTH  = 1400;
$MAX_HEIGHT = 1400;
$QUALITY    = 85; // JPEG kvalita 0-100
$CACHE_DIR  = __DIR__ . '/img/cache/';

// --- Validácia vstupu ---
$src = $_GET['src'] ?? '';
$src = ltrim(str_replace(['..', '\\'], '', $src), '/');

if (!$src || !preg_match('/\.(jpe?g|png|webp)$/i', $src)) {
    http_response_code(400); exit('Invalid file');
}

$srcPath = __DIR__ . '/' . $src;
if (!file_exists($srcPath) || !is_file($srcPath)) {
    http_response_code(404); exit('Not found');
}

// Len súbory z img/ foldra
if (!str_starts_with(realpath($srcPath), realpath(__DIR__ . '/img/'))) {
    http_response_code(403); exit('Forbidden');
}

// --- Cache cesta ---
if (!is_dir($CACHE_DIR)) {
    mkdir($CACHE_DIR, 0755, true);
}

$cacheFile = $CACHE_DIR . md5($src) . '.jpg';

// Servi cache ak existuje a je novšia ako originál
if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($srcPath)) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000'); // 30 dní
    readfile($cacheFile);
    exit;
}

// --- Načítaj obrázok ---
$ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
$src_img = match($ext) {
    'jpg', 'jpeg' => @imagecreatefromjpeg($srcPath),
    'png'         => @imagecreatefrompng($srcPath),
    'webp'        => @imagecreatefromwebp($srcPath),
    default       => false
};

if (!$src_img) {
    // GD nedokáže spracovať — servi originál
    header('Content-Type: ' . mime_content_type($srcPath));
    readfile($srcPath);
    exit;
}

$origW = imagesx($src_img);
$origH = imagesy($src_img);

// --- Vypočítaj nové rozmery ---
$ratio  = min($MAX_WIDTH / $origW, $MAX_HEIGHT / $origH, 1.0); // max 1.0 — nezväčšujeme
$newW   = (int) round($origW * $ratio);
$newH   = (int) round($origH * $ratio);

// --- Resize ---
$dst_img = imagecreatetruecolor($newW, $newH);

// Pre PNG: zachovaj priehľadnosť (konvertujeme na biele pozadie pre JPEG)
imagefill($dst_img, 0, 0, imagecolorallocate($dst_img, 255, 255, 255));
imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

imagedestroy($src_img);

// --- Ulož do cache a servi ---
imagejpeg($dst_img, $cacheFile, $QUALITY);
imagedestroy($dst_img);

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=2592000');
readfile($cacheFile);
