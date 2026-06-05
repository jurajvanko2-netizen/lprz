<?php
/**
 * LIPOREZ — Jednorazový migračný skript
 * Presunie existujúce obrázky z img/ do správnych podpriečinkov
 *
 * POUŽITIE: Spustite raz, potom VYMAŽTE tento súbor zo servera!
 * Lokálne: php migrate.php
 * Na serveri: navštívte https://vasadomena.sk/migrate.php?key=liporez_migrate
 */

define('MIGRATE_KEY', 'liporez_migrate');

// Bezpečnosť — vyžaduje tajný kľúč pri spustení cez web
if (PHP_SAPI !== 'cli' && ($_GET['key'] ?? '') !== MIGRATE_KEY) {
    http_response_code(403);
    die('Prístup odmietnutý. Pridajte ?key=' . MIGRATE_KEY . ' do URL.');
}

$imgRoot = __DIR__ . '/img/';

// Mapa: vzor názvu súboru => priečinok
$rules = [
    // Sakrálne predmety
    '/^sakralne/i'   => 'sakralne',
    '/^art_14_/i'    => 'sakralne',
    '/^art_15_/i'    => 'sakralne',
    '/^art_16_/i'    => 'sakralne',

    // Hudobné nástroje
    '/^art_26_/i'    => 'hudba',
    '/^art_27_/i'    => 'hudba',

    // Poľovnícke
    '/^polovnicke/i' => 'polovnicke',
    '/^art_17_/i'    => 'polovnicke',
    '/^art_18_/i'    => 'polovnicke',

    // Nábytok
    '/^nabytok/i'    => 'nabytok',
    '/^art_19_/i'    => 'nabytok',

    // Doplnky & sochy
    '/^doplnky/i'    => 'doplnky',
    '/^art_22_/i'    => 'doplnky',
    '/^art_24_/i'    => 'doplnky',
];

// Hero obrázok — necháme na mieste (je v index.php hardcoded)
$skipFiles = ['art_4_1.jpg'];

$results = [];
$errors = [];

// Načítaj všetky súbory priamo v img/
$files = glob($imgRoot . '*.{jpg,jpeg,png,JPG,JPEG,PNG,webp}', GLOB_BRACE);
if (!$files) $files = [];

foreach ($files as $filePath) {
    $filename = basename($filePath);

    if (in_array(strtolower($filename), array_map('strtolower', $skipFiles))) {
        $results[] = "⏭  Preskočené (hero): $filename";
        continue;
    }

    $destCat = null;
    foreach ($rules as $pattern => $category) {
        if (preg_match($pattern, $filename)) {
            $destCat = $category;
            break;
        }
    }

    if ($destCat === null) {
        $errors[] = "❓ Neznámy súbor (nezaradený): $filename";
        continue;
    }

    $destDir = $imgRoot . $destCat . '/';
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    $destPath = $destDir . $filename;
    if (file_exists($destPath)) {
        $results[] = "⚠️  Už existuje, preskočené: $destCat/$filename";
        continue;
    }

    if (rename($filePath, $destPath)) {
        $results[] = "✅ Presunuté: $filename → $destCat/";
    } else {
        $errors[] = "❌ Chyba presunu: $filename";
    }
}

// Výpis
if (PHP_SAPI === 'cli') {
    echo "=== LIPOREZ Migrácia ===\n\n";
    foreach ($results as $r) echo $r . "\n";
    if ($errors) {
        echo "\n=== CHYBY ===\n";
        foreach ($errors as $e) echo $e . "\n";
    }
    echo "\nHotovo. Vymažte tento súbor!\n";
} else {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Migrácia</title>";
    echo "<style>body{font-family:monospace;background:#1A0C04;color:#F0DEC0;padding:32px;}";
    echo "h1{color:#D4915A;margin-bottom:20px;} .ok{color:#80C880;} .err{color:#F08080;} .warn{color:#F0C870;}</style></head><body>";
    echo "<h1>LIPOREZ — Migrácia obrázkov</h1>";
    foreach ($results as $r) echo "<p>" . htmlspecialchars($r) . "</p>";
    if ($errors) {
        echo "<h2 style='color:#F08080;margin-top:20px;'>Chyby:</h2>";
        foreach ($errors as $e) echo "<p class='err'>" . htmlspecialchars($e) . "</p>";
    }
    echo "<p style='margin-top:32px;color:#D4915A;font-weight:bold;'>⚠️ VYMAŽTE tento súbor zo servera!</p>";
    echo "</body></html>";
}
