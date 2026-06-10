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
    file_put_contents(__DIR__.'/custom_texts.json', json_encode(new stdClass(), JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok'=>true]); exit;
}

// Všetky povolené kľúče
$ALLOWED_KEYS = [
    'logo_name','logo_tagline','nav_vyroba','nav_galeria','nav_omne','nav_kontakt',
    'hero_h1','hero_desc','hero_btn1','hero_btn2',
    'stat1_num','stat1_label','stat2_num','stat2_label',
    'stat3_num','stat3_label','stat4_num','stat4_label',
    'vyroba_eyebrow','vyroba_h2','vyroba_sub',
    'cat_sakralne_title','cat_sakralne_sub',
    'cat_hudba_title','cat_hudba_sub',
    'cat_polovnicke_title','cat_polovnicke_sub',
    'cat_nabytok_title','cat_nabytok_sub',
    'cat_doplnky_title','cat_doplnky_sub',
    'gal_sakralne_h3','gal_sakralne_p',
    'gal_hudba_h3','gal_hudba_p',
    'gal_polovnicke_h3','gal_polovnicke_p',
    'gal_nabytok_h3','gal_nabytok_p',
    'gal_doplnky_h3','gal_doplnky_p',
    'omne_eyebrow','omne_h2','omne_p1','omne_p2','omne_countries_intro',
    'cred1_title','cred1_text','cred2_title','cred2_text',
    'cred3_title','cred3_text','cred4_title','cred4_text',
    'feat1','feat2','feat3','feat4','feat5','feat6',
    'kontakt_eyebrow','kontakt_h2','kontakt_sub',
    'kontakt_adresa','kontakt_tel','kontakt_tel_href','kontakt_email','kontakt_ico',
    'footer_copy','footer_logo',
];

$out = [];
foreach ($ALLOWED_KEYS as $key) {
    if (isset($data[$key])) {
        $val = str_replace("\0", '', substr((string)$data[$key], 0, 2000));
        if ($key === 'hero_h1') {
            $val = strip_tags($val, '<br><em><strong>');
        }
        $out[$key] = $val;
    }
}

// Krajiny — dynamické pole
if (isset($data['omne_countries']) && is_array($data['omne_countries'])) {
    $countries = array_values(array_filter(array_map(function($c) {
        return str_replace("\0", '', substr(trim((string)$c), 0, 100));
    }, $data['omne_countries'])));
    $out['omne_countries'] = $countries;
}

file_put_contents(__DIR__.'/custom_texts.json', json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['ok'=>true]);
