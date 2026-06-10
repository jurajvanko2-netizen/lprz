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

// Sanitize hex colors
function sanitizeHex($val, $default) {
    $val = trim($val ?? $default);
    if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $val)) return $val;
    return $default;
}

$bg      = sanitizeHex($data['bg']     ?? '', '#F5EDE0');
$bg2     = sanitizeHex($data['bg2']    ?? '', '#EDE0CE');
$bgDark  = sanitizeHex($data['bgDark'] ?? '', '#0D0600');
$text    = sanitizeHex($data['text']   ?? '', '#1A0C04');
$border  = sanitizeHex($data['border'] ?? '', '#DDD0B8');
$accent  = sanitizeHex($data['accent'] ?? '', '#7C5230');
$fs      = intval($data['fontSize']    ?? 22);
$fs      = max(14, min(32, $fs));

// Uloz JSON pre znovunacitanie hodnot v admine
file_put_contents(__DIR__ . '/custom_vars.json', json_encode([
    'bg' => $bg, 'bg2' => $bg2, 'bgDark' => $bgDark,
    'text' => $text, 'border' => $border, 'accent' => $accent, 'fontSize' => $fs
]));

// Generuj CSS
$css  = "/* LIPOREZ custom styles — spravované cez admin.html */\n";
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
$css .= ".show-more-btn:hover { background: $accent !important; color: #F0DEC0 !important; }\n";
$css .= "section#omne { background: $bgDark !important; }\n";

file_put_contents(__DIR__ . '/custom.css', $css);
echo json_encode(['ok' => true]);
