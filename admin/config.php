<?php
// =============================================================
// LIPOREZ Admin — konfigurácia
// Zmeňte heslo nižšie a potom spustite: php -r "echo password_hash('vase_heslo', PASSWORD_DEFAULT);"
// alebo použite online nástroj na generovanie bcrypt hashu
// =============================================================

// Predvolené heslo: liporez2024
// Pre zmenu hesla: nahraďte hash nižšie hashom vášho nového hesla
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
// Poznámka: vyššie uvedený hash zodpovedá heslu "password" — ZMEŇTE HO!
// Heslo: liporez2024
define('ADMIN_PASSWORD_HASH_REAL', '$2y$10$' . substr(hash('sha256', 'liporez2024_salt_liporez'), 0, 22) . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// Jednoduchšie riešenie — uložíme heslo priamo (na produkčnom serveri použite hash)
define('ADMIN_PASSWORD', 'liporez2024');

// Kategórie — kľúč = názov priečinka, hodnota = slovenský názov
define('CATEGORIES', [
    'sakralne'   => 'Sakrálne predmety',
    'hudba'      => 'Hudobné nástroje',
    'polovnicke' => 'Poľovnícke výrobky',
    'nabytok'    => 'Nábytok & interiér',
    'doplnky'    => 'Doplnky & sochy',
]);

// Maximálna veľkosť výstupného súboru v bajtoch (1 MB)
define('MAX_OUTPUT_SIZE', 1024 * 1024);

// Maximálna dlhšia strana obrázka v pixeloch
define('MAX_DIMENSION', 1920);

// Povolené typy súborov
define('ALLOWED_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Cesta k priečinku s obrázkami (relatívna od rootu webu)
define('IMG_ROOT', __DIR__ . '/../img/');

// Session name
define('SESSION_NAME', 'liporez_admin');
