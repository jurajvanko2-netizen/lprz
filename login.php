<?php
session_name('liporez_site');
session_start();

define('SITE_PASSWORD', 'liporez2024'); // Zmeňte heslo tu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === SITE_PASSWORD) {
        $_SESSION['site_access'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Nesprávne heslo.';
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIPOREZ — Prístup</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #1A0C04; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: Arial, sans-serif; }
.box { background: #0D0600; border: 1px solid #3A1A08; border-radius: 10px; padding: 48px 40px; width: 340px; text-align: center; }
.logo { font-family: Georgia, serif; font-size: 28px; font-weight: bold; color: #F0DEC0; letter-spacing: 6px; margin-bottom: 8px; }
.sub { font-size: 12px; color: #5A3A20; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 36px; }
.error { background: rgba(180,30,30,0.2); border: 1px solid rgba(180,30,30,0.4); color: #F08080; font-size: 13px; padding: 10px; border-radius: 5px; margin-bottom: 18px; }
label { display: block; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; color: #9A7A5A; margin-bottom: 8px; text-align: left; }
input[type=password] { width: 100%; padding: 12px 14px; background: #180A02; border: 1px solid #3A1A08; border-radius: 5px; color: #F0DEC0; font-size: 15px; outline: none; margin-bottom: 18px; }
input[type=password]:focus { border-color: #7C5230; }
button { width: 100%; padding: 12px; background: #7C5230; color: #F0DEC0; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; letter-spacing: 1px; }
button:hover { background: #9A6A40; }
</style>
</head>
<body>
<div class="box">
    <div class="logo">LIPOREZ</div>
    <div class="sub">Rezbárstvo · Stolárstvo</div>
    <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="password">Heslo</label>
        <input type="password" id="password" name="password" autofocus>
        <button type="submit">Vstúpiť</button>
    </form>
</div>
</body>
</html>
