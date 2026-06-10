<?php
/**
 * LIPOREZ — Správa admin hesla
 * Heslo sa ukladá ako bcrypt hash do admin_password.txt
 * Ak súbor neexistuje, platí defaultné heslo 'liporez2024'
 */
define('ADMIN_PWD_FILE', __DIR__ . '/admin_password.txt');

function verifyAdminPassword(string $pwd): bool {
    if (file_exists(ADMIN_PWD_FILE)) {
        $hash = trim(file_get_contents(ADMIN_PWD_FILE));
        if ($hash) return password_verify($pwd, $hash);
    }
    // Žiadny súbor = defaultné heslo
    return $pwd === 'liporez2024';
}

function setAdminPassword(string $newPwd): bool {
    $hash = password_hash($newPwd, PASSWORD_BCRYPT);
    return file_put_contents(ADMIN_PWD_FILE, $hash) !== false;
}
