# LIPOREZ — Dokumentácia projektu

Webstránka Štefana Polacseka — rezbár a stolár od roku 1995.  
**Live URL:** https://liporez.labacik.com  
**Hosting:** Railway.app (automatický deploy z GitHubu)  
**Repozitár:** https://github.com/jurajvanko2-netizen/lprz.git, vetva `php-backend`

---

## Ako funguje deploy

Každý `git push origin php-backend` spustí automatický rebuild na Railway.  
Zmeny sú online zvyčajne do 1–2 minút.

```
lokálny súbor  →  git add + commit + push  →  GitHub  →  Railway (auto-deploy)  →  liporez.labacik.com
```

---

## Štruktúra súborov

```
/
├── index.php              ← Hlavná stránka (PHP, live)
├── index.html             ← Statická záloha (nie je live, nepoužíva sa)
├── login.php              ← Prihlásenie na stránku (heslo chráni celú stránku)
├── admin.html             ← Admin rozhranie (štýly, texty, heslo)
│
├── save_css.php           ← API: ukladá štýly z adminu do custom.css
├── save_texts.php         ← API: ukladá texty z adminu do custom_texts.json
├── admin_auth.php         ← API: overuje admin heslo pri prihlásení do adminu
├── change_password.php    ← API: mení admin heslo, zapisuje do admin_password.txt
├── password_config.php    ← Spoločná logika pre overovanie/zmenu admin hesla
├── img_cache.php          ← API: resize a cache obrázkov (max 1400px, JPEG 85%)
│
├── custom.css             ← Generované adminom — prepisuje farby a veľkosti
├── custom_texts.json      ← Generované adminom — uložené texty stránky
├── admin_password.txt     ← Generované serverom — bcrypt hash admin hesla
│                             (nie je v gite, vznikne prvou zmenou hesla)
│
├── Dockerfile             ← PHP 8.2 CLI server na Railway
├── .gitignore             ← Ignoruje img/cache/*.jpg a admin_password.txt
│
└── img/
    ├── cache/             ← Resizované obrázky (generované automaticky)
    ├── sakralne/          ← Fotky: sakrálne predmety
    ├── hudba/             ← Fotky: hudobné nástroje
    ├── polovnicke/        ← Fotky: poľovnícke výrobky
    ├── nabytok/           ← Fotky: nábytok a interiér
    ├── doplnky/           ← Fotky: doplnky a sochy
    └── other/             ← Ostatné fotky
```

---

## Dva typy hesiel

Projekt má **dve nezávislé heslá:**

| Heslo | Na čo slúži | Kde sa nastavuje |
|-------|-------------|------------------|
| **Heslo stránky** | Ochrana celej stránky pred návštevníkmi | `login.php`, riadok `define('SITE_PASSWORD', '...')` |
| **Admin heslo** | Prístup do admin rozhrania (/admin.html) | Záložka 🔑 Heslo v admine, ukladá sa do `admin_password.txt` |

Defaultné admin heslo (kým sa nezmení): `liporez2024`

---

## Ako funguje galéria

`index.php` automaticky načíta všetky obrázky z priečinkov `img/[kategória]/`.  
**Stačí nakopírovať fotku do správneho priečinka** — stránka ju zobrazí bez akejkoľvek zmeny kódu.

```
img/sakralne/   → sekcia Sakrálne predmety
img/hudba/      → sekcia Hudobné nástroje
img/polovnicke/ → sekcia Poľovnícke výrobky
img/nabytok/    → sekcia Nábytok & interiér
img/doplnky/    → sekcia Doplnky & sochy
```

Podporované formáty: `.jpg`, `.jpeg`, `.png`, `.webp` (veľké aj malé písmená)

### Automatický resize obrázkov

Každý obrázok sa pri prvom zobrazení automaticky zmenší na max 1400×1400 px a uloží do `img/cache/`.  
Lightbox (zväčšenie) otvára originálny súbor.  
Cache sa obnoví automaticky, ak je originál novší ako cache.

---

## Admin rozhranie

**URL:** `/admin.html`  
**Heslo:** defaultne `liporez2024`, meniteľné v záložke 🔑 Heslo

### Záložka 🎨 Štýly

Mení farby a veľkosti prvkov. Zmeny sa ukladajú do `custom.css` cez `save_css.php`.  
`custom.css` sa načítava v `index.php` ako posledný stylesheet — prepisuje všetko ostatné.  
**Reset** vymaže `custom.css` a `custom_vars.json` → stránka sa vráti na pôvodné farby.

### Záložka ✏️ Texty

Mení všetky texty na stránke. Zmeny sa ukladajú do `custom_texts.json` cez `save_texts.php`.  
`index.php` načíta `custom_texts.json` pri každom požiadavku a zobrazí uložené hodnoty.  
**Reset** vymaže `custom_texts.json` → stránka zobrazí hardcoded defaultné texty z `index.php`.

Sekcie textov:
- **Header & Navigácia** — logo, menu
- **Hero sekcia** — veľký nadpis, popis, tlačidlá
- **Štatistiky** — 4 čísla pod hero bannerom
- **Oblasti tvorby** — nadpis sekcie + 5 kategórií (názov + podnadpis)
- **Galéria — popisky** — H3 nadpis + popis pri každej galérii
- **Sekcia O mne** — biografia, krajiny (dynamický zoznam), 4 kartičky, 6 zručností
- **Kontakt & Footer** — adresa, telefón, e-mail, copyright

### Záložka 🔑 Heslo

Mení admin heslo. Nové heslo sa uloží ako bcrypt hash do `admin_password.txt` na serveri.  
Súbor nie je v gite — zostane na serveri aj po novom deplo.

---

## Kde čo meniť — rýchly prehľad

| Chcem zmeniť... | Kde |
|----------------|-----|
| Farby, veľkosti písma | Admin → 🎨 Štýly |
| Akýkoľvek text na stránke | Admin → ✏️ Texty |
| Admin heslo | Admin → 🔑 Heslo |
| Heslo na vstup na stránku | `login.php` — riadok `define('SITE_PASSWORD', '...')` |
| Pridať fotky do galérie | Nakopírovať `.jpg` do `img/[kategória]/` a pushnúť do gitu |
| Pridať novú kategóriu galérie | 1. Vytvoriť priečinok `img/nová_kategória/`, 2. Pridať do `$catPreviews` v `index.php` |
| Zmeniť štruktúru stránky | `index.php` — HTML/CSS/JS je tam celé |
| Zmeniť čo admin ponúka | `admin.html` + príslušný `save_*.php` |
| Zmeniť kvalitu/veľkosť cache obrázkov | `img_cache.php` — premenné `$MAX_WIDTH`, `$MAX_HEIGHT`, `$QUALITY` |

---

## Prepojenie súborov — diagram

```
Návštevník
    │
    ▼
login.php ──► index.php
                │
                ├── načíta: custom.css        (farby z adminu)
                ├── načíta: custom_texts.json (texty z adminu)
                └── obrázky cez: img_cache.php → img/cache/

Admin
    │
    ▼
admin.html
    ├── prihlásenie → admin_auth.php → password_config.php → admin_password.txt
    │
    ├── Záložka Štýly → save_css.php ──► custom.css + custom_vars.json
    ├── Záložka Texty → save_texts.php ──► custom_texts.json
    └── Záložka Heslo → change_password.php → password_config.php ──► admin_password.txt
```

---

## Súbory ktoré sa NEcommitujú (sú v .gitignore)

| Súbor | Dôvod |
|-------|-------|
| `admin_password.txt` | Obsahuje hash hesla — bezpečnostné riziko |
| `img/cache/*.jpg` atď. | Generované serverom — zbytočne zaberajú miesto v gite |

Tieto súbory existujú iba na serveri (Railway) a vznikajú automaticky počas behu.
