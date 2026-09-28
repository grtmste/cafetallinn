# Café Tallinn — WordPress theme

Custom classic theme for [cafetallinn.ee](https://cafetallinn.ee/). Installable zip: `dist/cafetallinn.zip` (zip root contains the `cafetallinn/` folder).

## Install

1. **Back up the site first** (database + `wp-content/uploads`).
2. WordPress admin → **Appearance → Themes → Add New → Upload Theme** → choose `cafetallinn.zip` → **Install Now** → **Activate**.
3. On activation the theme sets everything up (takes ~20 s; if the host is slow, a notice "pilte imporditakse… (x / 17)" appears — reload the admin page until it disappears):
   - Creates or adopts the pages Avaleht, Menüü, Joogikaart, Broneeri, Galerii, Kontakt, Catering, Perekaart, KKK and sets **Avaleht** as the static front page.
   - Existing Elementor pages are converted to clean content. The original Elementor content is **kept as a backup** in page meta (`_cafetallinn_backup_content`), nothing is deleted.
   - Adds the classes `js-open-menu` / `js-open-drinks` to the existing "Peamenüü" (or creates it) and assigns "Peamenüü" + "Jaluse menüü".
   - Imports the photos, logo and site icon into the Media Library (reuses files that already exist, e.g. `ANF_6902.jpg`).
   - Seeds **58 menu items** and **71 drinks** from `inc/data/*.json`.
   - Every step runs once. Re-activating the theme does not duplicate anything and does not bring back items you deleted. (To re-run from scratch: delete the option `cafetallinn_setup`.)
4. Recommended after checking the site: deactivate Elementor, Happy Addons, Helpie FAQ and Easy Accordion — the theme no longer needs them. Keep **Contact Form 7** (booking forms).

## Editing content

| What | Where |
|---|---|
| Address, phone, e-mail, opening hours, Facebook/Instagram, map (and whether it loads immediately) | **Appearance → Customize → Kohviku info** |
| Home page images (second hero photo, 4 tiles) | Customize → Kohviku info → Avalehe pildid. Main hero = featured image of page "Avaleht" |
| "Meie lugu" text | Pages → Avaleht |
| Page big heading (H1) and meta description | Page editor → box "Lehe pealkiri ja kirjeldus" |
| Gallery images | Pages → Galerii (edit the gallery block) |
| FAQ | Pages → KKK (Details blocks) |

### Menu and drink items

Admin → **Menüü ja joogid**.

- **Add item**: *Lisa uus* → title = dish name; in "Toote andmed" choose **Tüüp** (Menüü / Joogikaart), **Hind** exactly as it should appear (e.g. `9.00€` or `6.00€ / 8.00€`), **Kirjeldus**, **Sildid** (e.g. `V, GV`); tick a **Kategooria** on the right.
- **Order inside a category**: field *Järjekord* (Page Attributes box); smaller = higher.
- **Categories and their order**: Menüü ja joogid → **Kategooriad** → field *Järjekord* and *Kuulub* (Menüü/Joogikaart).
- Filter the list by type/category above the table.

Nav items with class `js-open-menu` / `js-open-drinks` open the dialogs (Appearance → Menus → Screen Options → enable "CSS Classes"). Links to `#menyy` and `#joogikaart` (also `#menyy-supid` etc.) open them on page load. Without JavaScript the links go to the full-page lists `/menuu/` and `/joogikaart/`.

## Booking form (Broneeri / Catering)

The page shows whatever form shortcode is in the page content. On the live site the existing Contact Form 7 forms are kept automatically. To change: create a form in **Contact → Contact Forms** (or WPForms), copy the shortcode, e.g. `[contact-form-7 id="05e7e64" title="Kontaktivorm 1"]` or `[wpforms id="123"]`, and paste it into the page **Broneeri** (Shortcode block). Styling for both plugins is built in.

## Development

- Source: `cafetallinn/assets/css/src/main.css`, `cafetallinn/assets/js/src/main.js` (vanilla JS, no jQuery). `SCRIPT_DEBUG` loads the unminified files.
- Build (minify + copy seed data + zip): `tools/build.sh` → `dist/cafetallinn.zip`.
- Scraped content: `/scrape` (`content.json`, `menu.json`, `drinks.json`, `contact.json`, `images/` + `mapping.json`). Transcription source: `tools/transcription.py`.

## Asset licenses

| Asset | Source | License |
|---|---|---|
| Cormorant Garamond (400/500/600, 400 italic; Latin + Latin Ext.) | Fontsource `@fontsource/cormorant-garamond` 5.3.0 | SIL Open Font License 1.1 (`assets/fonts/OFL-CormorantGaramond.txt`) |
| Jost variable (Latin + Latin Ext.) | Fontsource `@fontsource-variable/jost` 5.3.0 | SIL Open Font License 1.1 (`assets/fonts/OFL-Jost.txt`) |
| Icons, ornaments, corner brackets | Drawn for this theme (inline SVG / CSS) | GPL-2.0-or-later with the theme |
| Photos, logo | Café Tallinn's own material (existing site) | © Café Tallinn |

No external CDNs; fonts are self-hosted. The Google map on Kontakt loads immediately by default; untick **Customize → Kohviku info → Kaart → "Laadi kaart kohe"** to show a "Näita kaarti" button instead (Google is then contacted only after the click).

## Testing done

WordPress 7.1.2 + PHP 8.4 (SQLite), `WP_DEBUG` on: installed via the admin upload screen on (a) a fresh site and (b) a copy built from the live WXR export (Elementor pages, menus, CF7 forms). No PHP warnings/notices from the theme. Re-activation verified idempotent. Contact Form 7 6.1.7 tested on Broneeri (validation + success). Screenshots at 360/390/768/1024/1440 px, no horizontal scroll: see `/screenshots`.

Not tested: WPForms (plugin not obtainable in the build environment — styles target its standard markup), Theme Check plugin, Lighthouse score.
