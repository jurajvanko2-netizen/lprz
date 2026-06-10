<?php
/**
 * LIPOREZ — Hlavná stránka
 * Galérie sa načítavajú dynamicky z priečinkov img/[kategória]/
 */
session_name('liporez_site');
session_start();
if (!isset($_SESSION['site_access'])) {
    header('Location: login.php');
    exit;
}

function getGalleryImages(string $category): array {
    $dir = __DIR__ . '/img/' . $category . '/';
    if (!is_dir($dir)) return [];
    $files = glob($dir . '*.{jpg,jpeg,png,JPG,JPEG,PNG,webp,WEBP}', GLOB_BRACE);
    if (!$files) return [];
    sort($files);
    return array_map(fn($f) => 'img/' . $category . '/' . basename($f), $files);
}

$sakralne   = getGalleryImages('sakralne');
$hudba      = getGalleryImages('hudba');
$polovnicke = getGalleryImages('polovnicke');
$nabytok    = getGalleryImages('nabytok');
$doplnky    = getGalleryImages('doplnky');

function galleryItems(array $images, string $caption): string {
    if (empty($images)) {
        return '<p style="color:#9A7A5A;font-style:italic;font-size:15px;">Žiadne obrázky.</p>';
    }
    $out = '';
    foreach ($images as $img) {
        $src = htmlspecialchars($img);
        $cap = htmlspecialchars($caption);
        $out .= '<div class="gallery-item" onclick="openLightbox(\'' . $src . '\',\'' . $cap . '\')">';
        $out .= '<img src="' . $src . '" alt="' . $cap . ' — LIPOREZ" loading="lazy">';
        $out .= '</div>';
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Štefan Polacsek LIPOREZ | Rezbárstvo a stolárstvo na zákazku — Malé Košecké Podhradie</title>
<meta name="description" content="Rezbárstvo a stolárstvo na zákazku od roku 1995. Sakrálne predmety, hudobné nástroje, poľovnícke štítky, nábytok, sochy. Malé Košecké Podhradie.">
<meta name="keywords" content="rezbárstvo, stolárstvo, rezby na zákazku, sakrálne predmety, poľovnícke štítky, hudobné nástroje, nábytok na mieru, reštaurovanie nábytku, dláto, Košecké Podhradie, Slovensko">
<meta name="author" content="Štefan Polacsek — LIPOREZ">
<meta name="robots" content="index, follow">
<meta name="geo.region" content="SK">
<meta name="geo.placename" content="Malé Košecké Podhradie">

<meta property="og:type" content="website">
<meta property="og:url" content="https://liporez.szm.sk/index.html">
<meta property="og:title" content="LIPOREZ — Rezbárstvo a stolárstvo na zákazku od roku 1995">
<meta property="og:description" content="Štefan Polacsek vyrába a vyrezáva na zákazku: sakrálne predmety, nábytok, poľovnícke štítky, hudobné nástroje a sochy. Bez brúsneho papiera. Malé Košecké Podhradie.">
<meta property="og:image" content="https://liporez.szm.sk/Liporez_files/art_4_1.jpg">
<meta property="og:image:width" content="640">
<meta property="og:image:height" content="853">
<meta property="og:locale" content="sk_SK">
<meta property="og:site_name" content="LIPOREZ">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="LIPOREZ — Rezbárstvo a stolárstvo na zákazku">
<meta name="twitter:description" content="Štefan Polacsek — rezbár a stolár od roku 1995. Malé Košecké Podhradie, Slovensko.">
<meta name="twitter:image" content="https://liporez.szm.sk/Liporez_files/art_4_1.jpg">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "LIPOREZ",
  "description": "Rezbárstvo a stolárstvo na zákazku od roku 1995. Sakrálne predmety, nábytok, poľovnícke štítky, hudobné nástroje, sochy a doplnky.",
  "url": "https://liporez.szm.sk",
  "telephone": "+421905815775",
  "email": "liporez@centrum.sk",
  "foundingDate": "1995",
  "founder": {
    "@type": "Person",
    "name": "Štefan Polacsek"
  },
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Háj č. 251",
    "addressLocality": "Malé Košecké Podhradie",
    "postalCode": "018 31",
    "addressCountry": "SK"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "48.98",
    "longitude": "18.29"
  },
  "areaServed": ["Slovensko", "Česká republika", "Rakúsko", "Nemecko", "Francúzsko", "Anglicko", "USA", "Rumunsko"],
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Rezbárske a stolárske výrobky",
    "itemListElement": [
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Sakrálne predmety"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Rezby na hudobné nástroje"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Poľovnícke štítky a trofeje"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Nábytok na zákazku"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Reštaurovanie starožitného nábytku"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Dekoratívne sochy a doplnky"}}
    ]
  },
  "award": "Cena organizátora Czech-ART Festival 2011, Drevo-Sochy, České Budejovice",
  "image": "https://liporez.szm.sk/Liporez_files/art_4_1.jpg",
  "taxID": "47359021"
}
</script>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='-50 -50 100 100'%3E%3Crect x='-49' y='-49' width='98' height='98' rx='2' fill='%231A0C04' stroke='%23B8864E' stroke-width='1.5'/%3E%3Cline x1='-49' y1='-32' x2='-32' y2='-49' stroke='%23B8864E' stroke-width='0.8'/%3E%3Cline x1='32' y1='-49' x2='49' y2='-32' stroke='%23B8864E' stroke-width='0.8'/%3E%3Cline x1='-49' y1='32' x2='-32' y2='49' stroke='%23B8864E' stroke-width='0.8'/%3E%3Cline x1='32' y1='49' x2='49' y2='32' stroke='%23B8864E' stroke-width='0.8'/%3E%3Ctext x='0' y='12' font-family='Georgia,serif' font-size='18' font-weight='bold' fill='%23F0DEC0' text-anchor='middle' letter-spacing='2'%3ELIPOREZ%3C/text%3E%3Cline x1='-35' y1='18' x2='35' y2='18' stroke='%23B8864E' stroke-width='0.7'/%3E%3C/svg%3E">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }
  body { font-family: Georgia, 'Times New Roman', serif; font-size: 22px; background: #F5EDE0; color: #1A0C04; }

  /* TOPBAR */
  .topbar {
    background: #080300;
    padding: 9px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: Arial, sans-serif;
    font-size: 15px;
    color: #C4A882;
  }
  .topbar a { color: #C4A882; text-decoration: none; }
  .topbar a:hover { color: #F0DEC0; }
  .topbar-contacts { display: flex; gap: 24px; }
  .topbar-contacts span { display: flex; align-items: center; gap: 6px; }

  /* HEADER */
  header {
    background: #0D0600;
    padding: 20px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #5A3010;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  .logo { display: flex; align-items: center; gap: 14px; text-decoration: none; }
  .logo-icon {
    width: 48px; height: 48px;
    background: #6B4220;
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
  }
  .logo-name {
    font-family: Arial, sans-serif;
    font-size: 22px;
    font-weight: bold;
    color: #F0DEC0;
    letter-spacing: 5px;
    display: block;
  }
  .logo-tagline {
    font-family: Arial, sans-serif;
    font-size: 15px;
    color: #7A6050;
    letter-spacing: 2px;
    text-transform: uppercase;
    display: block;
  }
  nav { display: flex; gap: 2px; align-items: center; }
  nav a {
    font-family: Arial, sans-serif;
    font-size: 13px;
    color: #B09070;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 4px;
    transition: color 0.2s, background 0.2s;
  }
  nav a:hover { color: #F0DEC0; background: rgba(124,82,48,0.25); }
  .nav-cta { background: #7C5230 !important; color: #F0DEC0 !important; padding: 8px 18px !important; }
  .nav-cta:hover { background: #9A6A40 !important; }

  /* HERO */
  .hero {
    background: #0D0600;
    padding: 70px 40px 80px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
  }
  .hero-tag {
    display: inline-block;
    font-family: Arial, sans-serif;
    font-size: 10px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #C4A882;
    background: rgba(124,82,48,0.2);
    border: 1px solid rgba(124,82,48,0.4);
    padding: 5px 14px;
    border-radius: 3px;
    margin-bottom: 22px;
  }
  .hero h1 { font-size: 62px; font-weight: normal; color: #F0DEC0; line-height: 1.2; margin-bottom: 18px; }
  .hero h1 em { color: #D4915A; font-style: normal; }
  .hero-desc { font-family: Arial, sans-serif; font-size: 20px; color: #C4A882; line-height: 1.8; margin-bottom: 32px; }
  .hero-btns { display: flex; gap: 14px; flex-wrap: wrap; }
  .btn-primary {
    font-family: Arial, sans-serif; font-size: 14px;
    background: #7C5230; color: #F0DEC0;
    border: none; padding: 13px 28px; border-radius: 5px;
    cursor: pointer; text-decoration: none; display: inline-block;
    transition: background 0.2s;
  }
  .btn-primary:hover { background: #9A6A40; }
  .btn-outline {
    font-family: Arial, sans-serif; font-size: 14px;
    background: transparent; color: #B09070;
    border: 1px solid #6B4220; padding: 13px 28px; border-radius: 5px;
    cursor: pointer; text-decoration: none; display: inline-block;
    transition: border-color 0.2s, color 0.2s;
  }
  .btn-outline:hover { border-color: #9A6A40; color: #D4B896; }

  /* STATS */
  .stats-bar {
    background: #180A02;
    border-top: 1px solid #2A1208; border-bottom: 1px solid #2A1208;
    display: flex; justify-content: center;
  }
  .stat { flex: 1; max-width: 200px; text-align: center; padding: 28px 20px; border-right: 1px solid #2A1208; }
  .stat:last-child { border-right: none; }
  .stat-num { font-size: 34px; font-weight: normal; color: #D4915A; display: block; margin-bottom: 4px; }
  .stat-label { font-family: Arial, sans-serif; font-size: 13px; color: #9A7A5A; text-transform: uppercase; letter-spacing: 1.5px; }

  /* SECTIONS */
  section { padding: 72px 40px; }
  .section-light { background: #F5EDE0; }
  .section-cream { background: #EDE0CE; }
  .section-dark { background: #0D0600; }

  .section-eyebrow { display: none; }
  .section-eyebrow-light { display: none; }
  h2 { font-size: 44px; font-weight: normal; color: #1A0C04; margin-bottom: 10px; }
  h2.light { color: #F0DEC0; }
  .section-sub { font-family: Arial, sans-serif; font-size: 20px; color: #7A5A3A; line-height: 1.7; margin-bottom: 44px; max-width: 600px; }
  .section-sub.light { color: #8A6A4A; }

  /* CATEGORY OVERVIEW */
  .cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
  .cat-card { background: #FAF4EC; border: 1px solid #DDD0B8; border-radius: 10px; overflow: hidden; transition: border-color 0.2s, transform 0.2s; }
  .cat-card:hover { border-color: #9A6A40; transform: translateY(-2px); }
  .cat-card-img { width: 100%; height: 180px; object-fit: cover; display: block; cursor: pointer; transition: transform 0.3s; }
  .cat-card:hover .cat-card-img { transform: scale(1.03); }
  .cat-card-body { padding: 16px 18px; }
  .cat-card-title { font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; color: #1A0C04; margin-bottom: 4px; }
  .cat-card-sub { font-family: Arial, sans-serif; font-size: 14px; color: #9A7A5A; }

  /* CAT DETAIL SECTIONS */
  .cat-section { padding: 60px 40px; }
  .cat-section:nth-child(odd) { background: #F5EDE0; }
  .cat-section:nth-child(even) { background: #EDE0CE; }
  .cat-section-inner { display: flex; flex-direction: column; gap: 28px; }
  .cat-info { max-width: 720px; }
  .cat-info h3 { font-size: 32px; font-weight: normal; color: #1A0C04; margin-bottom: 10px; }
  .cat-info p { font-family: Arial, sans-serif; font-size: 19px; color: #7A5A3A; line-height: 1.8; margin-bottom: 0; }
  .cat-label { display: none; }

  /* GALLERY */
  .gallery-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
  .gallery-item { border-radius: 6px; overflow: hidden; cursor: pointer; aspect-ratio: 4/3; background: #D4C4AE; }
  .gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
  .gallery-item:hover img { transform: scale(1.06); }

  /* ABOUT */
  .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
  .about-left { display: flex; flex-direction: column; }
  .about-badge {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: Arial, sans-serif; font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
    color: #B09070; background: rgba(124,82,48,0.18); border: 1px solid rgba(124,82,48,0.35);
    padding: 5px 14px; border-radius: 3px; margin-bottom: 18px; align-self: flex-start;
  }
  .about-left h2 { margin-bottom: 18px; }
  .about-left p { font-family: Arial, sans-serif; font-size: 19px; color: #8A6A4A; line-height: 1.85; margin-bottom: 18px; }
  .features { list-style: none; margin-bottom: 24px; }
  .features li {
    font-family: Arial, sans-serif; font-size: 18px; color: inherit;
    padding: 7px 0; border-bottom: 1px solid rgba(124,82,48,0.15);
    display: flex; align-items: center; gap: 10px; line-height: 1.5;
  }
  .features li::before { content: ''; width: 6px; height: 6px; background: #D4915A; border-radius: 50%; flex-shrink: 0; }
  .countries { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
  .country-pill {
    font-family: Arial, sans-serif; font-size: 15px; color: #C4A882;
    background: rgba(124,82,48,0.15); border: 1px solid rgba(124,82,48,0.3);
    padding: 4px 12px; border-radius: 20px;
  }

  /* CRED CARDS */
  .cred-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-content: start; }
  .cred-card {
    background: rgba(124,82,48,0.1); border: 1px solid rgba(124,82,48,0.25);
    border-radius: 8px; padding: 16px 18px;
    display: flex; flex-direction: column; gap: 4px;
  }
  .cred-icon { display: none; }
  .cred-title { font-family: Arial, sans-serif; font-size: 17px; font-weight: bold; color: #D4B896; }
  .cred-text { font-family: Arial, sans-serif; font-size: 15px; color: #7A5A3A; line-height: 1.5; }
  .cred-card.featured {
    background: rgba(180,110,30,0.15);
    border: 2px solid #B8864E;
    position: relative;
    overflow: hidden;
  }
  .cred-card.featured .cred-title { color: #F0C87A; font-size: 16px; }
  .cred-card.featured .cred-text { color: #9A7A5A; }

  /* KONTAKT */
  .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
  .contact-rows { display: flex; flex-direction: column; gap: 20px; }
  .contact-row { display: flex; align-items: center; gap: 16px; }
  .contact-icon { display: none; }
  .contact-label { font-family: Arial, sans-serif; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; color: #7C5230; margin-bottom: 3px; }
  .contact-val { font-family: Arial, sans-serif; font-size: 17px; font-weight: bold; color: #7C5230; }
  .contact-val a { color: #7C5230; text-decoration: none; }
  .contact-val a:hover { text-decoration: underline; }
  .map-embed { border-radius: 10px; overflow: hidden; border: 1px solid #C4B09A; height: 280px; }
  .map-embed iframe { width: 100%; height: 100%; border: none; display: block; }

  /* FOOTER */
  footer {
    background: #080300; padding: 32px 40px;
    display: flex; justify-content: space-between; align-items: center;
    border-top: 1px solid #1A0A02;
  }
  .footer-copy { font-family: Arial, sans-serif; font-size: 12px; color: #4A2E14; }
  .footer-logo { font-family: Arial, sans-serif; font-size: 18px; font-weight: bold; color: #5A3010; letter-spacing: 4px; }
  .footer-motto { font-size: 13px; color: #5A3A20; font-style: italic; }

  /* LIGHTBOX */
  .lightbox {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.92); z-index: 999;
    align-items: center; justify-content: center; padding: 20px;
  }
  .lightbox.open { display: flex; }
  .lightbox-img { max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 4px; display: block; }
  .lightbox-close { position: fixed; top: 20px; right: 28px; font-size: 36px; color: #C4A882; cursor: pointer; background: none; border: none; font-family: Arial, sans-serif; }
  .lightbox-close:hover { color: #fff; }
  .lightbox-nav { position: fixed; top: 50%; transform: translateY(-50%); font-size: 40px; color: #C4A882; background: none; border: none; cursor: pointer; padding: 10px 20px; font-family: Arial, sans-serif; }
  .lightbox-nav:hover { color: #fff; }
  .lightbox-prev { left: 10px; }
  .lightbox-next { right: 10px; }
  .lightbox-caption { position: fixed; bottom: 24px; left: 0; right: 0; text-align: center; font-family: Arial, sans-serif; font-size: 13px; color: #C4A882; }

  hr.divider { border: none; border-top: 1px solid #D4C4AE; }

  /* MAX-WIDTH */
  .topbar, header, footer {
    padding-left: max(32px, calc((100% - 1240px) / 2));
    padding-right: max(32px, calc((100% - 1240px) / 2));
  }
  .hero {
    padding-left: max(40px, calc((100% - 1240px) / 2));
    padding-right: max(40px, calc((100% - 1240px) / 2));
  }
  section {
    padding-left: max(40px, calc((100% - 1240px) / 2));
    padding-right: max(40px, calc((100% - 1240px) / 2));
  }
  .cat-section {
    padding-left: max(40px, calc((100% - 1240px) / 2));
    padding-right: max(40px, calc((100% - 1240px) / 2));
  }
  .stats-bar {
    padding-left: max(0px, calc((100% - 1240px) / 2));
    padding-right: max(0px, calc((100% - 1240px) / 2));
  }

  @media (max-width: 768px) {
    body { font-size: 16px; }
    .topbar { flex-direction: column; gap: 6px; padding: 10px 20px; text-align: center; font-size: 13px; }
    .topbar-contacts { flex-direction: column; gap: 4px; align-items: center; }
    header { padding: 14px 20px; flex-wrap: wrap; gap: 12px; }
    header svg { width: 60px !important; height: 60px !important; }
    .logo-name { font-size: 18px; }
    .logo-tagline { font-size: 12px; }
    nav { gap: 0; flex-wrap: wrap; justify-content: flex-end; }
    nav a { font-size: 12px; padding: 6px 10px; }
    .hero { grid-template-columns: 1fr; padding: 40px 20px; gap: 28px; }
    .hero h1 { font-size: 36px; }
    .hero-desc { font-size: 15px; }
    .stats-bar { flex-wrap: wrap; }
    .stat { max-width: none; flex: 1 1 45%; border-right: none; border-bottom: 1px solid #2A1208; padding: 18px 12px; }
    .stat-num { font-size: 26px; }
    section { padding: 48px 20px; }
    .section-sub { font-size: 15px; }
    h2 { font-size: 30px; }
    .cat-grid { grid-template-columns: 1fr 1fr; }
    .cat-section { padding: 40px 20px; }
    .cat-section-inner { gap: 20px; }
    .cat-info h3 { font-size: 22px; }
    .cat-info p { font-size: 15px; }
    .gallery-grid { grid-template-columns: repeat(3, 1fr); }
    .about-grid { grid-template-columns: 1fr; gap: 32px; }
    .cred-grid { grid-template-columns: 1fr 1fr; }
    .features { columns: 1; }
    .contact-grid { grid-template-columns: 1fr; gap: 28px; }
    .map-embed { height: 220px; }
    footer { flex-direction: column; gap: 10px; text-align: center; padding: 24px 20px; }
    section#omne { padding-left: 20px !important; padding-right: 20px !important; }
  }

  /* SHOW MORE */
  .show-more-btn {
    display: block;
    margin: 24px auto 0;
    font-family: Arial, sans-serif;
    font-size: 14px;
    background: transparent;
    color: #7C5230;
    border: 1px solid #7C5230;
    padding: 10px 32px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    letter-spacing: 0.5px;
  }
  .show-more-btn:hover { background: #7C5230; color: #F0DEC0; }

  @media (max-width: 480px) {
    .hero h1 { font-size: 28px; }
    .cat-grid { grid-template-columns: 1fr; }
    .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    .cred-grid { grid-template-columns: 1fr; }
    nav a { font-size: 11px; padding: 5px 8px; }
    .stat { flex: 1 1 100%; }
  }
</style>
<link rel="stylesheet" href="custom.css">
</head>
<body>

<!-- HEADER -->
<header>
  <a href="#" class="logo" style="text-decoration:none; display:flex; align-items:center; gap:18px;">
    <svg viewBox="-90 -90 180 180" width="90" height="90" xmlns="http://www.w3.org/2000/svg">
      <rect x="-88" y="-88" width="176" height="176" rx="2" fill="#1A0C04" stroke="#B8864E" stroke-width="1.5"/>
      <line x1="-88" y1="-58" x2="-58" y2="-88" stroke="#B8864E" stroke-width="0.8"/>
      <line x1="58" y1="-88" x2="88" y2="-58" stroke="#B8864E" stroke-width="0.8"/>
      <line x1="-88" y1="58" x2="-58" y2="88" stroke="#B8864E" stroke-width="0.8"/>
      <line x1="58" y1="88" x2="88" y2="58" stroke="#B8864E" stroke-width="0.8"/>
      <text x="-18" y="28" font-family="Georgia,serif" font-size="80" font-weight="bold" fill="#B8864E" text-anchor="middle" opacity="0.25">L</text>
      <text x="18" y="28" font-family="Georgia,serif" font-size="80" font-weight="bold" fill="#F0DEC0" text-anchor="middle" opacity="0.15">R</text>
      <text x="0" y="15" font-family="Georgia,serif" font-size="28" font-weight="bold" fill="#F0DEC0" text-anchor="middle" letter-spacing="4">LIPOREZ</text>
      <line x1="-62" y1="24" x2="62" y2="24" stroke="#B8864E" stroke-width="0.7"/>
      <text x="0" y="38" font-family="Arial" font-size="6.5" fill="#9A6A40" text-anchor="middle" letter-spacing="2.5">REZBÁRSTVO · STOLÁRSTVO</text>
      <text x="0" y="52" font-family="Arial" font-size="6" fill="#5A3010" text-anchor="middle" letter-spacing="3">EST. 1995</text>
    </svg>
    <div>
      <span class="logo-name">LIPOREZ</span>
      <span class="logo-tagline">Rezbárstvo &amp; Stolárstvo od 1995</span>
    </div>
  </a>
  <nav>
    <a href="#vyroba">Výroba</a>
    <a href="#galeria">Galéria</a>
    <a href="#omne">O mne</a>
    <a href="#kontakt" class="nav-cta">Kontakt</a>
  </nav>
</header>

<!-- HERO -->
<div class="hero">
  <div class="hero-text">
    <h1>Precíznosť,<br>ktorú <em>vidieť</em><br>v každom reze</h1>
    <p class="hero-desc">
      Vyrábam a vyrezávam na zákazku kompletné nábytkárske výrobky, rezbárske fragmenty, sakrálne predmety, hudobné nástroje i poľovnícke štítky. Začisťujem bez brúsneho papiera.
    </p>
    <div class="hero-btns">
      <a href="#vyroba" class="btn-primary">Pozrieť výrobky</a>
      <a href="#kontakt" class="btn-outline">Kontaktovať</a>
    </div>
  </div>
  <div style="border-radius:10px; overflow:hidden; cursor:pointer; height:100%;"
       onclick="openLightbox('img/art_4_1.jpg','Ukážka rezbárskej práce')">
    <img src="img/art_4_1.jpg" alt="Ukážka rezbárskej práce Štefan Polacsek LIPOREZ"
         style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.3s;">
  </div>
</div>

<!-- STATS -->
<div class="stats-bar">
  <div class="stat"><span class="stat-num">30+</span><span class="stat-label">rokov tradície</span></div>
  <div class="stat"><span class="stat-num">8</span><span class="stat-label">krajín sveta</span></div>
  <div class="stat"><span class="stat-num">100%</span><span class="stat-label">ručná práca</span></div>
  <div class="stat"><span class="stat-num">0</span><span class="stat-label">brúsny papier</span></div>
</div>

<!-- VÝROBA PREHĽAD -->
<section id="vyroba" class="section-light">
  <span class="section-eyebrow">Čo robím</span>
  <h2>Oblasti tvorby</h2>
  <p class="section-sub">Každý výrobok je originál. Pracujem takmer so všetkými druhmi dreva, podľa priania zákazníka.</p>
  <div class="cat-grid">
    <?php
    $catPreviews = [
        'sakralne'   => ['img/sakralne/sakralne1.jpg', 'img/art_4_1.jpg', 'Sakrálne predmety', 'Kríže, relikvie, oltárne rezby'],
        'hudba'      => ['img/hudba/art_26_1.jpg',     'img/art_4_1.jpg', 'Hudobné nástroje',  'Cimbal, husle, zdobenie nástrojov'],
        'polovnicke' => ['img/polovnicke/polovnicke1.jpg','img/art_4_1.jpg','Poľovnícke',       'Štítky, pažby, trofeje'],
        'nabytok'    => ['img/nabytok/nabytok1.jpg',   'img/art_4_1.jpg', 'Nábytok',           'Kuchynské linky, schodiská, kópie'],
        'doplnky'    => ['img/doplnky/doplnky10.jpg',  'img/art_4_1.jpg', 'Doplnky &amp; sochy','Dekorácie, figúry, darčeky'],
    ];
    foreach ($catPreviews as $catKey => [$imgPref, $imgFallback, $title, $sub]):
        // Try to get first image from the category folder
        $catImgs = getGalleryImages($catKey);
        $previewImg = !empty($catImgs) ? $catImgs[0] : $imgFallback;
    ?>
    <div class="cat-card">
      <img class="cat-card-img" src="<?= htmlspecialchars($previewImg) ?>" alt="<?= $title ?> — rezbárstvo na zákazku LIPOREZ"
           onclick="openLightbox('<?= htmlspecialchars($previewImg) ?>','<?= $title ?>')">
      <div class="cat-card-body">
        <div class="cat-card-title"><?= $title ?></div>
        <div class="cat-card-sub"><?= $sub ?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <div class="cat-card" style="display:flex;align-items:center;justify-content:center;min-height:220px;border-style:dashed;background:transparent;">
      <div style="text-align:center;font-family:Arial,sans-serif;color:#9A7A5A;font-size:14px;line-height:1.8;">
        Máte špeciálnu<br>požiadavku?<br>
        <a href="#kontakt" style="color:#7C5230;font-weight:bold;text-decoration:none;">Napíšte mi →</a>
      </div>
    </div>
  </div>
</section>

<!-- O MNE -->
<section id="omne" style="background:#2A1608; padding-top:72px; padding-bottom:72px; padding-left: max(40px, calc((100% - 1240px) / 2)); padding-right: max(40px, calc((100% - 1240px) / 2))">
  <span class="section-eyebrow section-eyebrow-light">Remeselník</span>
  <div class="about-grid">
    <!-- VĽAVO: nadpis, text -->
    <div class="about-left">
      <h2 class="light">Rezbár a stolár s viac ako 30 rokmi skúseností</h2>
      <p style="color:#C4A882;">Rezbárčine som sa venoval v Strednom odbornom učilišti drevárskom v Oradei (rumunskom Varadíne). Po presťahovaní z Rumunska na Slovensko v roku 1990 som pôsobil vo Zvolene — Bučine, neskôr v umeleckom rezbárstve špecializovanom na luxusné nábytky v Dubnici nad Váhom.</p>
      <p style="color:#C4A882;">Vyrábam takmer zo všetkých druhov dreva. Začisťovanie vykonávam bez použitia brúsneho papiera. Kvalitná povrchová úprava nemeckými a talianskými lakmi či olejmi, podľa priania zákazníka.</p>
      <p style="font-style:italic;font-size:19px;color:#B09070;margin-top:24px;margin-bottom:12px;">Moje výrobky sa nachádzajú v:</p>
      <div class="countries">
        <span class="country-pill">Česká republika</span>
        <span class="country-pill">Rakúsko</span>
        <span class="country-pill">Nemecko</span>
        <span class="country-pill">Francúzsko</span>
        <span class="country-pill">Anglicko</span>
        <span class="country-pill">USA</span>
        <span class="country-pill">Rumunsko</span>
      </div>
    </div>

    <!-- VPRAVO: kartičky -->
    <div class="about-right">
      <div class="cred-grid">
        <div class="cred-card featured" style="padding:12px 14px;">
          <div class="cred-title" style="font-size:17px;">Czech-ART Festival 2011</div>
          <div class="cred-text" style="font-size:15px;">Cena organizátora Drevo-Sochy, České Budejovice</div>
        </div>
        <div class="cred-card featured" style="padding:12px 14px;">
          <div class="cred-title" style="font-size:17px;">Bratislavský hrad</div>
          <div class="cred-text" style="font-size:15px;">Rám na obraz Márie Terézie na zrekonštrukovanom hrade</div>
        </div>
        <div class="cred-card featured" style="padding:12px 14px;">
          <div class="cred-title" style="font-size:17px;">Všetky druhy dreva</div>
          <div class="cred-text" style="font-size:15px;">Dub, orech, lipa, buk, čerešňa, agát a ďalšie</div>
        </div>
        <div class="cred-card featured" style="padding:12px 14px;">
          <div class="cred-title" style="font-size:17px;">Od roku 1995</div>
          <div class="cred-text" style="font-size:15px;">Kvalita a serióznosť je moje krédo</div>
        </div>
      </div>
    </div>

  </div>
  <ul class="features" style="margin-top:40px; columns:2; column-gap:60px; color:#C4A882;">
    <li>Čistota rezu — začisťovanie bez brúsneho papiera</li>
    <li>Nemecké a talianske laky a oleje</li>
    <li>Reštaurovanie starožitného nábytku</li>
    <li>Kópie historických rezieb a nábytkov</li>
    <li>Zdobenie hudobných nástrojov</li>
    <li>Úprava pažieb strelných zbraní</li>
  </ul>
</section>

<hr class="divider">

<!-- SAKRÁLNE -->
<div id="galeria" class="cat-section" style="background:#F5EDE0;">
  <div class="cat-section-inner">
    <div class="cat-info">
      <span class="cat-label">⛪ Kategória</span>
      <h3>Sakrálne predmety</h3>
      <p>Kríže, svätostánky, relikvie a oltárne rezby. Precízna práca pre kostoly, kaplnky i súkromných zberateľov. Reštaurovanie historických sakrálnych predmetov.</p>
    </div>
    <div class="gallery-grid">
      <?= galleryItems($sakralne, 'Sakrálna rezba') ?>
    </div>
  </div>
</div>

<hr class="divider">

<!-- HUDOBNÉ NÁSTROJE -->
<div class="cat-section" style="background:#EDE0CE;">
  <div class="cat-section-inner">
    <div class="cat-info">
      <span class="cat-label">🎵 Kategória</span>
      <h3>Rezby na hudobné nástroje</h3>
      <p>Zdobím cimbaly, husle a iné ľudové nástroje. Na fotografiách rezby na cimbal HOLÁK majstra Vladimíra Holiša z Kozlovíc pre ľudovú hudbu Rosenka z Košarísk.</p>
    </div>
    <div class="gallery-grid">
      <?= galleryItems($hudba, 'Hudobný nástroj — rezba') ?>
    </div>
  </div>
</div>

<hr class="divider">

<!-- POĽOVNÍCKE -->
<div class="cat-section" style="background:#F5EDE0;">
  <div class="cat-section-inner">
    <div class="cat-info">
      <span class="cat-label">🦌 Kategória</span>
      <h3>Poľovnícke výrobky</h3>
      <p>Trofejné poľovnícke štítky, úprava a zdobenie pažieb strelných zbraní, parohy. Aj sochy s poľovníckou tematikou — Diana bohyňa lovu (výška 70 cm).</p>
    </div>
    <div class="gallery-grid">
      <?= galleryItems($polovnicke, 'Poľovnícky výrobok') ?>
    </div>
  </div>
</div>

<hr class="divider">

<!-- NÁBYTOK -->
<div class="cat-section" style="background:#EDE0CE;">
  <div class="cat-section-inner">
    <div class="cat-info">
      <span class="cat-label">🛋 Kategória</span>
      <h3>Nábytok &amp; interiér</h3>
      <p>Kuchynské linky, schodiská, historické kópie nábytku. Reštaurovanie starožitného nábytku a výroba luxusných nábytkov na mieru. Povrchovú úpravu robím nemeckými a talianskými lakmi či olejmi.</p>
    </div>
    <div class="gallery-grid">
      <?= galleryItems($nabytok, 'Nábytok') ?>
    </div>
  </div>
</div>

<hr class="divider">

<!-- DOPLNKY -->
<div class="cat-section" style="background:#F5EDE0;">
  <div class="cat-section-inner">
    <div class="cat-info">
      <span class="cat-label">🎨 Kategória</span>
      <h3>Doplnky &amp; sochy</h3>
      <p>Dekoratívne predmety, figúry a sochy v životnej veľkosti, darčeky na objednávku. Každý kus je unikátny — sústruženie dreva, reliéfy, trojrozmerné rezby.</p>
    </div>
    <div class="gallery-grid">
      <?= galleryItems($doplnky, 'Doplnok & socha') ?>
    </div>
  </div>
</div>

<!-- KONTAKT -->
<section id="kontakt" class="section-light">
  <span class="section-eyebrow">Kde ma nájdete</span>
  <h2>Kontakt</h2>
  <p class="section-sub">Napíšte alebo zavolajte — rád sa dohovorím na individuálnej zákazke.</p>
  <div class="contact-grid">
    <div class="contact-rows">
      <div class="contact-row">
        <div class="contact-icon">📍</div>
        <div>
          <div class="contact-label">Adresa</div>
          <div class="contact-val">Malé Košecké Podhradie, Háj č. 251<br>018 31 Košecké Podhradie</div>
        </div>
      </div>
      <div class="contact-row">
        <div class="contact-icon">📞</div>
        <div>
          <div class="contact-label">Telefón</div>
          <div class="contact-val"><a href="tel:+421905815775">+421 905 815 775</a></div>
        </div>
      </div>
      <div class="contact-row">
        <div class="contact-icon">✉</div>
        <div>
          <div class="contact-label">E-mail</div>
          <div class="contact-val"><a href="mailto:liporez@centrum.sk">liporez@centrum.sk</a></div>
        </div>
      </div>
      <div class="contact-row">
        <div class="contact-icon">🪪</div>
        <div>
          <div class="contact-label">IČO / DIČ</div>
          <div class="contact-val">47359021 / 2023831326</div>
        </div>
      </div>
    </div>
    <div class="map-embed">
      <iframe
        src="https://maps.google.com/maps?q=Male+Kosecke+Podhradie&output=embed"
        allowfullscreen="" loading="lazy"
        title="Mapa — Malé Košecké Podhradie">
      </iframe>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-copy">© <?= date('Y') ?> Štefan Polacsek — LIPOREZ &nbsp;·&nbsp; IČO: 47359021</div>
  <div class="footer-logo">LIPOREZ</div>
</footer>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
  <button class="lightbox-close" onclick="closeLb()">✕</button>
  <button class="lightbox-nav lightbox-prev" onclick="lbNav(-1);event.stopPropagation();">‹</button>
  <img class="lightbox-img" id="lightbox-img" src="" alt="">
  <button class="lightbox-nav lightbox-next" onclick="lbNav(1);event.stopPropagation();">›</button>
  <div class="lightbox-caption" id="lightbox-caption"></div>
</div>

<script>
  var allImgs = [];
  var lbIdx = 0;

  document.querySelectorAll('.gallery-item').forEach(function(el) {
    var img = el.querySelector('img');
    if (img) allImgs.push({ src: img.src, cap: img.alt || '' });
  });

  function openLightbox(src, cap) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-caption').textContent = cap || '';
    document.getElementById('lightbox').classList.add('open');
    lbIdx = allImgs.findIndex(function(i){ return i.src.endsWith(src.split('/').pop()); });
    document.body.style.overflow = 'hidden';
  }
  function closeLb() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
  }
  function closeLightbox(e) { if (e.target === document.getElementById('lightbox')) closeLb(); }
  function lbNav(dir) {
    if (!allImgs.length) return;
    lbIdx = (lbIdx + dir + allImgs.length) % allImgs.length;
    document.getElementById('lightbox-img').src = allImgs[lbIdx].src;
    document.getElementById('lightbox-caption').textContent = allImgs[lbIdx].cap;
  }
  document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('lightbox');
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowLeft') lbNav(-1);
    if (e.key === 'ArrowRight') lbNav(1);
  });

  // EQUALIZE CRED CARDS
  (function() {
    var cards = Array.from(document.querySelectorAll('#omne .cred-card.featured'));
    if (!cards.length) return;
    var maxH = Math.max.apply(null, cards.map(function(c) { return c.offsetHeight; }));
    cards.forEach(function(c) { c.style.minHeight = maxH + 'px'; });
  })();

  // SHOW MORE
  (function() {
    var PAGE_SIZE = 15;
    document.querySelectorAll('.gallery-grid').forEach(function(grid) {
      var items = Array.from(grid.querySelectorAll('.gallery-item'));
      if (items.length <= PAGE_SIZE) return;
      var shown = PAGE_SIZE;
      items.forEach(function(item, i) {
        if (i >= PAGE_SIZE) item.style.display = 'none';
      });
      var btn = document.createElement('button');
      btn.className = 'show-more-btn';
      btn.textContent = 'Ukázať viac (' + (items.length - shown) + ')';
      grid.insertAdjacentElement('afterend', btn);
      btn.addEventListener('click', function() {
        var nextShown = Math.min(shown + PAGE_SIZE, items.length);
        for (var i = shown; i < nextShown; i++) {
          items[i].style.display = '';
        }
        shown = nextShown;
        if (shown >= items.length) {
          btn.style.display = 'none';
        } else {
          btn.textContent = 'Ukázať viac (' + (items.length - shown) + ')';
        }
      });
    });
  })();
</script>
</body>
</html>
