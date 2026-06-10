<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit;
}

require_once __DIR__ . '/password_config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !verifyAdminPassword($data['password'] ?? '')) {
    http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Nesprávne heslo']); exit;
}

if (!empty($data['reset'])) {
    file_put_contents(__DIR__.'/custom.css', "/* LIPOREZ custom styles — spravované cez admin.html */\n");
    file_put_contents(__DIR__.'/custom_vars.json', json_encode([]));
    echo json_encode(['ok'=>true]); exit;
}

function hex($val, $default) {
    $val = trim($val ?? $default);
    return preg_match('/^#[0-9a-fA-F]{3,6}$/', $val) ? $val : $default;
}
function px($val, $min, $max, $default) {
    return max($min, min($max, intval($val ?? $default)));
}

// Nadpisy
$h2Color  = hex($data['h2Color']  ?? '', '#1A0C04');
$h3Color  = hex($data['h3Color']  ?? '', '#1A0C04');
$h2Size   = px($data['h2Size']    ?? 44, 28, 60, 44);
$h3Size   = px($data['h3Size']    ?? 32, 20, 48, 32);

// Svetlé sekcie
$bg       = hex($data['bg']       ?? '', '#F5EDE0');
$bg2      = hex($data['bg2']      ?? '', '#EDE0CE');
$text     = hex($data['text']     ?? '', '#1A0C04');
$border   = hex($data['border']   ?? '', '#DDD0B8');
$accent   = hex($data['accent']   ?? '', '#7C5230');
$fs       = px($data['fontSize']  ?? 22, 14, 32, 22);

// Štatistiky
$statsBg    = hex($data['statsBg']    ?? '', '#180A02');
$statNum    = hex($data['statNum']    ?? '', '#D4915A');
$statLabel  = hex($data['statLabel']  ?? '', '#9A7A5A');

// Sekcia O mne
$bgDark        = hex($data['bgDark']        ?? '', '#2A1608');
$textDark      = hex($data['textDark']      ?? '', '#C4A882');
$featuresColor = hex($data['featuresColor'] ?? '', '#C4A882');
$pillColor     = hex($data['pillColor']     ?? '', '#C4A882');

// Kartičky
$credBg     = hex($data['credBg']     ?? '', '#2d1810');
$credBorder = hex($data['credBorder'] ?? '', '#B8864E');
$credTitle  = hex($data['credTitle']  ?? '', '#F0C87A');
$credText   = hex($data['credText']   ?? '', '#9A7A5A');
$credFs     = px($data['credFontSize'] ?? 15, 11, 22, 15);

// Päta
$footerBg   = hex($data['footerBg']   ?? '', '#080300');
$footerCopy = hex($data['footerCopy'] ?? '', '#4A2E14');
$footerLogo = hex($data['footerLogo'] ?? '', '#5A3010');

// Ulož JSON
file_put_contents(__DIR__.'/custom_vars.json', json_encode([
    'h2Color'=>$h2Color,'h3Color'=>$h3Color,'h2Size'=>$h2Size,'h3Size'=>$h3Size,
    'bg'=>$bg,'bg2'=>$bg2,'text'=>$text,'border'=>$border,'accent'=>$accent,'fontSize'=>$fs,
    'statsBg'=>$statsBg,'statNum'=>$statNum,'statLabel'=>$statLabel,
    'bgDark'=>$bgDark,'textDark'=>$textDark,'featuresColor'=>$featuresColor,'pillColor'=>$pillColor,
    'credBg'=>$credBg,'credBorder'=>$credBorder,'credTitle'=>$credTitle,'credText'=>$credText,'credFontSize'=>$credFs,
    'footerBg'=>$footerBg,'footerCopy'=>$footerCopy,'footerLogo'=>$footerLogo
]));

// Generuj CSS
$css = "/* LIPOREZ custom styles — spravované cez admin.html */\n\n";

$css .= "/* Nadpisy */\n";
$css .= "h2:not(.light) { color: $h2Color !important; font-size: {$h2Size}px !important; }\n";
$css .= ".cat-info h3 { color: $h3Color !important; font-size: {$h3Size}px !important; }\n\n";

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

$css .= "/* Štatistiky */\n";
$css .= ".stats-bar { background: $statsBg !important; }\n";
$css .= ".stat-num { color: $statNum !important; }\n";
$css .= ".stat-label { color: $statLabel !important; }\n\n";

$css .= "/* Sekcia O mne */\n";
$css .= "section#omne { background: $bgDark !important; }\n";
$css .= "section#omne p, section#omne .about-left p { color: $textDark !important; }\n";
$css .= "section#omne .features li { color: $featuresColor !important; }\n";
$css .= ".country-pill { color: $pillColor !important; }\n\n";

$css .= "/* Kartičky */\n";
$css .= ".cred-card.featured { background: $credBg !important; border-color: $credBorder !important; }\n";
$css .= ".cred-card.featured .cred-title { color: $credTitle !important; font-size: {$credFs}px !important; }\n";
$css .= ".cred-card.featured .cred-text { color: $credText !important; font-size: {$credFs}px !important; }\n\n";

$css .= "/* Päta */\n";
$css .= "footer { background: $footerBg !important; }\n";
$css .= ".footer-copy { color: $footerCopy !important; }\n";
$css .= ".footer-logo { color: $footerLogo !important; }\n";

file_put_contents(__DIR__.'/custom.css', $css);
echo json_encode(['ok'=>true]);
