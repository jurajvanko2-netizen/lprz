<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || ($data['password'] ?? '') !== 'liporez2024') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Nesprávne heslo']);
    exit;
}

// Reset — vymaze custom.css
if (!empty($data['reset'])) {
    file_put_contents(__DIR__ . '/custom.css', "/* LIPOREZ custom styles — spravované cez admin.html */\n");
    file_put_contents(__DIR__ . '/custom_vars.json', json_encode([]));
    echo json_encode(['ok' => true]);
    exit;
}

function sanitizeHex($val, $default) {
    $val = trim($val ?? $default);
    if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $val)) return $val;
    return $default;
}

// Svetlé sekcie
$bg         = sanitizeHex($data['bg']         ?? '', '#F5EDE0');
$bg2        = sanitizeHex($data['bg2']        ?? '', '#EDE0CE');
$text       = sanitizeHex($data['text']       ?? '', '#1A0C04');
$border     = sanitizeHex($data['border']     ?? '', '#DDD0B8');
$accent     = sanitizeHex($data['accent']     ?? '', '#7C5230');
$fs         = max(14, min(32, intval($data['fontSize']     ?? 22)));

// Tmavá sekcia O mne
$bgDark     = sanitizeHex($data['bgDark']     ?? '', '#2A1608');
$textDark   = sanitizeHex($data['textDark']   ?? '', '#C4A882');

// Kartičky cred-card
$credBg     = sanitizeHex($data['credBg']     ?? '', '#2d1810');
$credBorder = sanitizeHex($data['credBorder'] ?? '', '#B8864E');
$credTitle  = sanitizeHex($data['credTitle']  ?? '', '#F0C87A');
$credText   = sanitizeHex($data['credText']   ?? '', '#9A7A5A');
$credFs     = max(11, min(22, intval($data['credFontSize'] ?? 15)));

// Uloz JSON pre znovunacitanie hodnot v admine
file_put_contents(__DIR__ . '/custom_vars.json', json_encode([
    'bg' => $bg, 'bg2' => $bg2, 'bgDark' => $bgDark,
    'text' => $text, 'border' => $border, 'accent' => $accent,
    'textDark' => $textDark,
    'credBg' => $credBg, 'credBorder' => $credBorder,
    'credTitle' => $credTitle, 'credText' => $credText,
    'fontSize' => $fs, 'credFontSize' => $credFs
]));

// Generuj CSS
$css  = "/* LIPOREZ custom styles — spravované cez admin.html */\n\n";

// Svetlé sekcie
$css .= "/* Svetlé sekcie */\n";
$css .= "body { background: $bg !important; color: $text !important; font-size: {$fs}px !important; }\n";
$css .= ".section-light { background: $bg !important; }\n";
$css .= ".section-cream { background: $bg2 !important; }\n";
$css .= ".cat-section:nth-child(odd) { background: $bg !important; }\n";
$css .= ".cat-section:nth-child(even) { background: $bg2 !important; }\n";
$css .= ".cat-card { border-color: $border !important; }\n";
$css .= ".btn-primary { background: $accent !important; }\n";
$css .= ".btn-primary:hover { background: $accent !important; filter: brightness(1.15); }\n";
$css .= ".nav-cta { background: $accent !important; }\n";
$css .= ".nav-cta:hover { background: $accent !important; filter: brightness(1.15); }\n";
$css .= ".show-more-btn { color: $accent !important; border-color: $accent !important; }\n";
$css .= ".show-more-btn:hover { background: $accent !important; color: #F0DEC0 !important; }\n\n";

// Tmavá sekcia O mne
$css .= "/* Tmavá sekcia O mne */\n";
$css .= "section#omne { background: $bgDark !important; }\n";
$css .= "section#omne p, section#omne .about-left p { color: $textDark !important; }\n\n";

// Kartičky cred-card
$css .= "/* Kartičky */\n";
$css .= ".cred-card.featured { background: $credBg !important; border-color: $credBorder !important; }\n";
$css .= ".cred-card.featured .cred-title { color: $credTitle !important; font-size: {$credFs}px !important; }\n";
$css .= ".cred-card.featured .cred-text { color: $credText !important; font-size: {$credFs}px !important; }\n";

file_put_contents(__DIR__ . '/custom.css', $css);
echo json_encode(['ok' => true]);
