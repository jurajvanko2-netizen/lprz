<?php
require_once 'config.php';
session_name(SESSION_NAME);
session_start();

// Prihlásenie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Nesprávne heslo.';
    }
}

// Odhlásenie
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Načítaj obrázky pre každú kategóriu
function getImages($category) {
    $dir = IMG_ROOT . $category . '/';
    if (!is_dir($dir)) return [];
    $files = glob($dir . '*.{jpg,jpeg,png,JPG,JPEG,PNG,webp,WEBP}', GLOB_BRACE);
    if (!$files) return [];
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    return array_map(fn($f) => basename($f), $files);
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIPOREZ — Správa galérie</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #1A0C04; color: #F0DEC0; min-height: 100vh; }

/* HEADER */
.admin-header {
    background: #080300;
    padding: 16px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #5A3010;
}
.admin-header h1 { font-size: 20px; letter-spacing: 4px; color: #F0DEC0; font-weight: bold; }
.admin-header span { font-size: 13px; color: #7A5A3A; }
.logout-btn { font-size: 13px; color: #C4A882; text-decoration: none; background: rgba(124,82,48,0.2); border: 1px solid #5A3010; padding: 6px 14px; border-radius: 4px; }
.logout-btn:hover { background: rgba(124,82,48,0.4); }

/* LOGIN */
.login-wrap { display: flex; align-items: center; justify-content: center; min-height: 80vh; }
.login-box { background: #0D0600; border: 1px solid #3A1A08; border-radius: 10px; padding: 40px 36px; width: 340px; }
.login-box h2 { font-size: 22px; margin-bottom: 8px; color: #F0DEC0; letter-spacing: 2px; }
.login-box p { font-size: 14px; color: #7A5A3A; margin-bottom: 28px; }
.login-error { background: rgba(180,30,30,0.2); border: 1px solid rgba(180,30,30,0.4); color: #F08080; font-size: 14px; padding: 10px 14px; border-radius: 5px; margin-bottom: 18px; }
label { display: block; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; color: #9A7A5A; margin-bottom: 6px; }
input[type=password], input[type=text] {
    width: 100%; padding: 11px 14px; background: #180A02; border: 1px solid #3A1A08;
    border-radius: 5px; color: #F0DEC0; font-size: 15px; outline: none;
}
input:focus { border-color: #7C5230; }
.btn { width: 100%; padding: 12px; background: #7C5230; color: #F0DEC0; border: none; border-radius: 5px; font-size: 15px; cursor: pointer; margin-top: 18px; letter-spacing: 1px; }
.btn:hover { background: #9A6A40; }

/* MAIN LAYOUT */
.admin-main { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }

/* UPLOAD PANEL */
.upload-panel {
    background: #0D0600; border: 1px solid #3A1A08; border-radius: 10px;
    padding: 28px 28px; margin-bottom: 36px;
}
.upload-panel h2 { font-size: 18px; color: #F0DEC0; margin-bottom: 20px; letter-spacing: 2px; }
.upload-form { display: grid; grid-template-columns: 1fr 1fr auto; gap: 16px; align-items: end; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
select {
    width: 100%; padding: 11px 14px; background: #180A02; border: 1px solid #3A1A08;
    border-radius: 5px; color: #F0DEC0; font-size: 15px; outline: none; cursor: pointer;
}
select:focus { border-color: #7C5230; }
.file-input-wrap {
    position: relative; display: flex; align-items: center;
    background: #180A02; border: 1px solid #3A1A08; border-radius: 5px;
    padding: 10px 14px; cursor: pointer; gap: 10px;
}
.file-input-wrap:hover { border-color: #7C5230; }
.file-input-wrap input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }
.file-label { font-size: 14px; color: #9A7A5A; }
.file-name { font-size: 14px; color: #C4A882; }
.upload-btn {
    padding: 11px 28px; background: #7C5230; color: #F0DEC0; border: none;
    border-radius: 5px; font-size: 15px; cursor: pointer; white-space: nowrap;
}
.upload-btn:hover { background: #9A6A40; }
.upload-btn:disabled { background: #3A1A08; color: #5A3A20; cursor: not-allowed; }

/* PROGRESS & STATUS */
.upload-status { margin-top: 16px; font-size: 14px; display: none; }
.upload-status.success { color: #80C880; }
.upload-status.error { color: #F08080; }
.progress-bar-wrap { margin-top: 12px; background: #180A02; border-radius: 4px; height: 6px; display: none; }
.progress-bar { height: 6px; background: #7C5230; border-radius: 4px; width: 0%; transition: width 0.3s; }

/* GALLERY SECTIONS */
.cat-section { margin-bottom: 40px; }
.cat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #2A1208; padding-bottom: 12px; }
.cat-header h3 { font-size: 16px; letter-spacing: 2px; color: #D4915A; text-transform: uppercase; }
.cat-count { font-size: 13px; color: #5A3A20; }
.img-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
.img-card { position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 4/3; background: #0D0600; border: 1px solid #2A1208; }
.img-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
.img-card-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.6);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 8px; opacity: 0; transition: opacity 0.2s;
}
.img-card:hover .img-card-overlay { opacity: 1; }
.img-filename { font-size: 11px; color: #C4A882; text-align: center; padding: 0 8px; word-break: break-all; }
.delete-btn {
    background: rgba(180,30,30,0.8); color: #fff; border: none;
    padding: 6px 14px; border-radius: 4px; font-size: 12px; cursor: pointer;
}
.delete-btn:hover { background: rgba(220,50,50,0.9); }
.empty-cat { color: #3A1A08; font-size: 14px; font-style: italic; padding: 20px 0; }
</style>
</head>
<body>

<div class="admin-header">
    <h1>LIPOREZ <span style="font-weight:normal;letter-spacing:1px;font-size:16px;">— Správa galérie</span></h1>
    <?php if ($loggedIn): ?>
    <a href="?logout=1" class="logout-btn">Odhlásiť sa</a>
    <?php endif; ?>
</div>

<?php if (!$loggedIn): ?>
<!-- LOGIN -->
<div class="login-wrap">
    <div class="login-box">
        <h2>PRIHLÁSENIE</h2>
        <p>Zadajte heslo pre správu galérie</p>
        <?php if (!empty($loginError)): ?>
        <div class="login-error"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div style="margin-bottom:18px;">
                <label for="password">Heslo</label>
                <input type="password" id="password" name="password" autofocus>
            </div>
            <button type="submit" class="btn">Prihlásiť sa</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ADMIN PANEL -->
<div class="admin-main">

    <!-- UPLOAD PANEL -->
    <div class="upload-panel">
        <h2>NAHRAŤ OBRÁZOK</h2>
        <div class="upload-form">
            <div class="form-group">
                <label>Kategória</label>
                <select id="upload-category">
                    <?php foreach (CATEGORIES as $key => $label): ?>
                    <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Obrázok (JPG, PNG, WebP)</label>
                <div class="file-input-wrap">
                    <input type="file" id="upload-file" accept="image/jpeg,image/png,image/webp" multiple>
                    <span class="file-label" id="file-label-text">📁 Vybrať súbory...</span>
                    <span class="file-name" id="file-name-display"></span>
                </div>
            </div>
            <button class="upload-btn" id="upload-btn" onclick="startUpload()">Nahrať</button>
        </div>
        <div class="progress-bar-wrap" id="progress-wrap"><div class="progress-bar" id="progress-bar"></div></div>
        <div class="upload-status" id="upload-status"></div>
    </div>

    <!-- GALLERIES PER CATEGORY -->
    <?php foreach (CATEGORIES as $catKey => $catLabel): ?>
    <?php $images = getImages($catKey); ?>
    <div class="cat-section" id="cat-<?= $catKey ?>">
        <div class="cat-header">
            <h3><?= htmlspecialchars($catLabel) ?></h3>
            <span class="cat-count"><?= count($images) ?> obrázkov</span>
        </div>
        <?php if (empty($images)): ?>
        <p class="empty-cat">Žiadne obrázky v tejto kategórii.</p>
        <?php else: ?>
        <div class="img-grid" id="grid-<?= $catKey ?>">
            <?php foreach ($images as $fname): ?>
            <div class="img-card" id="card-<?= $catKey ?>-<?= htmlspecialchars(preg_replace('/[^a-z0-9_-]/i', '', $fname)) ?>">
                <img src="../img/<?= $catKey ?>/<?= htmlspecialchars($fname) ?>" alt="<?= htmlspecialchars($fname) ?>" loading="lazy">
                <div class="img-card-overlay">
                    <span class="img-filename"><?= htmlspecialchars($fname) ?></span>
                    <button class="delete-btn" onclick="deleteImage('<?= $catKey ?>', '<?= htmlspecialchars($fname) ?>', this)">Vymazať</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

</div>

<script>
// File picker display
document.getElementById('upload-file').addEventListener('change', function() {
    const files = this.files;
    if (files.length === 0) {
        document.getElementById('file-name-display').textContent = '';
        document.getElementById('file-label-text').textContent = '📁 Vybrať súbory...';
    } else if (files.length === 1) {
        document.getElementById('file-name-display').textContent = files[0].name;
        document.getElementById('file-label-text').textContent = '';
    } else {
        document.getElementById('file-name-display').textContent = files.length + ' súborov vybraných';
        document.getElementById('file-label-text').textContent = '';
    }
});

async function startUpload() {
    const fileInput = document.getElementById('upload-file');
    const category = document.getElementById('upload-category').value;
    const statusEl = document.getElementById('upload-status');
    const btn = document.getElementById('upload-btn');
    const progressWrap = document.getElementById('progress-wrap');
    const progressBar = document.getElementById('progress-bar');

    if (!fileInput.files.length) {
        showStatus('Vyberte aspoň jeden súbor.', 'error');
        return;
    }

    btn.disabled = true;
    progressWrap.style.display = 'block';
    progressBar.style.width = '0%';

    const files = Array.from(fileInput.files);
    let uploaded = 0;
    let errors = [];

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fd = new FormData();
        fd.append('category', category);
        fd.append('image', file);

        try {
            const resp = await fetch('upload.php', { method: 'POST', body: fd });
            const data = await resp.json();
            if (data.success) {
                uploaded++;
                addImageToGrid(category, data.filename, data.path);
            } else {
                errors.push(file.name + ': ' + data.error);
            }
        } catch(e) {
            errors.push(file.name + ': sieťová chyba');
        }
        progressBar.style.width = Math.round(((i + 1) / files.length) * 100) + '%';
    }

    btn.disabled = false;
    fileInput.value = '';
    document.getElementById('file-name-display').textContent = '';
    document.getElementById('file-label-text').textContent = '📁 Vybrať súbory...';

    if (errors.length === 0) {
        showStatus('✓ Nahraných ' + uploaded + ' obrázkov.', 'success');
    } else if (uploaded > 0) {
        showStatus('✓ ' + uploaded + ' nahraných. Chyby: ' + errors.join('; '), 'error');
    } else {
        showStatus('Chyba: ' + errors.join('; '), 'error');
    }
}

function addImageToGrid(category, filename, path) {
    let grid = document.getElementById('grid-' + category);
    const catSection = document.getElementById('cat-' + category);

    // Ak neexistuje grid, vytvor ho (bola tam "empty" správa)
    if (!grid) {
        const emptyP = catSection.querySelector('.empty-cat');
        if (emptyP) emptyP.remove();
        grid = document.createElement('div');
        grid.className = 'img-grid';
        grid.id = 'grid-' + category;
        catSection.appendChild(grid);
    }

    const safeId = filename.replace(/[^a-z0-9_-]/gi, '');
    const card = document.createElement('div');
    card.className = 'img-card';
    card.id = 'card-' + category + '-' + safeId;
    card.innerHTML = `
        <img src="../${path}" alt="${filename}" loading="lazy">
        <div class="img-card-overlay">
            <span class="img-filename">${filename}</span>
            <button class="delete-btn" onclick="deleteImage('${category}', '${filename}', this)">Vymazať</button>
        </div>`;
    grid.insertBefore(card, grid.firstChild);

    // Aktualizuj počet
    const countEl = catSection.querySelector('.cat-count');
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        countEl.textContent = (current + 1) + ' obrázkov';
    }
}

async function deleteImage(category, filename, btn) {
    if (!confirm('Naozaj vymazať: ' + filename + '?')) return;

    btn.disabled = true;
    btn.textContent = '...';

    const fd = new FormData();
    fd.append('category', category);
    fd.append('filename', filename);

    try {
        const resp = await fetch('delete.php', { method: 'POST', body: fd });
        const data = await resp.json();
        if (data.success) {
            const safeId = filename.replace(/[^a-z0-9_-]/gi, '');
            const card = document.getElementById('card-' + category + '-' + safeId);
            if (card) card.remove();

            // Aktualizuj počet
            const catSection = document.getElementById('cat-' + category);
            const countEl = catSection ? catSection.querySelector('.cat-count') : null;
            if (countEl) {
                const current = Math.max(0, (parseInt(countEl.textContent) || 1) - 1);
                countEl.textContent = current + ' obrázkov';
            }
        } else {
            alert('Chyba: ' + data.error);
            btn.disabled = false;
            btn.textContent = 'Vymazať';
        }
    } catch(e) {
        alert('Sieťová chyba.');
        btn.disabled = false;
        btn.textContent = 'Vymazať';
    }
}

function showStatus(msg, type) {
    const el = document.getElementById('upload-status');
    el.textContent = msg;
    el.className = 'upload-status ' + type;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 6000);
}
</script>

<?php endif; ?>
</body>
</html>
