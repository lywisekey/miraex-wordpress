# Miraex build scripts

Idempotent PHP scripts that generate the Miraex site chrome and pages as **native
BeBuilder content** (sections → wraps → items in `mfn-page-items`), per
`../MIRAEX-BEBUILDER-MAPPING.md`.

Source of truth for content & design: `miraex.com/html-redesign/`
(`index.html` + `css/app.css`), also deployed at
<https://ashy-forest-0b7587303.7.azurestaticapps.net/>.

## Running them

The site's DB host is `mysql` (docker network), so run these inside the laradock
workspace container, not on the host:

```sh
docker cp lib.php <script>.php laradock-workspace-1:/tmp/
docker exec laradock-workspace-1 php /tmp/<script>.php
```

`lib.php` must be next to the script — every builder script requires it.

## Order

| # | Script | What it does |
|---|---|---|
| 1 | `import_media.php` | Pulls the reference site's images into the media library, tagging each with a `_miraex_src_url` meta so re-runs skip them. |
| 2 | `import_svg.php` | Same, for `imd.svg` (needs a temporary SVG mime allowance). |
| 3 | `import_clients.php` | Creates the 14 `client` posts (logo + outbound link) behind the "Backed & recognised by" strip. |
| 4 | `build_menus.php` | Creates the **Miraex Main Menu** nav menu (Solutions + 4 children, Technology, Company, News, Resources, Careers). The header reads it by term id. |
| 5 | `set_theme_options.php` | Sets `background-html` / `background-body` to `#070f1c` so no white shows behind the dark design. |
| 6 | `build_header.php` | Rebuilds the **Header Miraex** template: three sections (`ver` = `default` transparent-over-hero, `header-sticky` dark+blur on scroll, `header-mobile` logo+burger), sets `header_position=fixed`, sticky and mobile headers enabled, and assigns it site-wide. |
| 7 | `build_footer.php` | Creates the **Footer Miraex** template: 4-column link grid + bottom bar, assigned site-wide via `mfn_footer_entire_site`. |
| 8 | ~~`build_cf7.php`~~ | Superseded: the contact page posts to HubSpot now. Kept only for the record of what the CF7 form contained. |
| 9 | `build_home.php` | Builds the homepage, sets it as the front page, hides the title area and BeTheme's content padding. |
| 10 | `build_pages.php` | Builds the 12 inner pages: technology, the three solution pages, about, news + its 3 article child pages, resources, careers, contact. |
| 11 | `build_section_templates.php` | Saves the 21 distinct section shapes used across the pages as `template` posts with `mfn_template_type = 'section'`, so any of them can be dropped into a page from BeBuilder. Reads the sections back out of the built pages, so a template can never drift from the live design. Run **after** `build_home.php` / `build_pages.php`. |
| 12 | `build_megamenu.php` | Creates the **Mega menu — Solutions** template (620px panel, 2×2 grid of icon + title + description). Does **not** attach itself to a menu item — see below. |
| 13 | `install_rankmath.php` | Downloads and activates Rank Math SEO from wordpress.org. Forces the `direct` filesystem method — otherwise `Plugin_Upgrader` asks for FTP credentials and just returns `false` on the CLI. |
| 14 | `migrate_seo.php` | Moves the 12 `_miraex_meta_description` values into `rank_math_description`, fixes the Site Title, sets the front-page title/description, gives every page an `og:image`, and flushes the rewrite rules so the sitemap resolves. |
| — | `lib_hubspot.php` | The contact form: markup + styling + the browser-side POST to HubSpot. Required by `build_pages.php`; not run on its own. |
| 15 | `install_litespeed.php` | Downloads and activates LiteSpeed Cache. |
| 16 | `configure_litespeed.php` | Applies the cache and optimisation settings, with the reasoning for each in the file. List settings are stored as **arrays**, scalars as strings — writing the wrong shape leaves a setting that reads correctly in the database and is ignored by the plugin. |
| — | `dump_sections.php` | Dev helper: lists every section on every page with its title, wrap/item counts and element types. This is how the section library was inventoried. |
| — | `export_site.php`/`export_site.sh` | Packages the database and `wp-content` for handover — see `../DEPLOY.md`. |
| — | `verify.php` | Sanity check: section/wrap/item counts, item types, generated fonts and CSS sizes. |
| — | `dump_fields.php` | Dev helper: prints a BeBuilder item type's real field ids, selectors and defaults. Run this before adding new elements. |
| — | `dump_repeater.php` | Dev helper: prints the shape of repeater fields (`accordion`, `timeline`, …). |
| — | `audit_widths.php` | Checks that every custom wrap/item width also has `width_switcher = custom`, so the builder UI and the generated CSS cannot disagree. |

`lib.php` holds the design tokens, the section/wrap/item constructors, the shared
element helpers (`heading_item`, `text_item`, `button_item`, `eyebrow`, `card_attr`)
and `mfn_store_template()`, which writes the builder meta and regenerates
`mfn-page-local-style` + `uploads/betheme/css/post-<id>.css` the same way
BeBuilder's own save routine does.

`lib_page.php` holds the page-level components that mirror `app.css`: `page_hero()`
(breadcrumbs + H1 + lead over a radial glow), `cta_band()`, `head_items()`,
`tick_items()`, `spec_table()`, `app_items()` / `app_card_wraps()`,
`feature_card()` / `feature_cards()`, `card_item()`, `timeline_items()`,
`stat_item()`, `clients_strip()` and `framed_image()`. `build_home.php` requires it too,
so the homepage and the inner pages share one definition per component.

## Editing after the fact

Everything is native BeBuilder, so all of it is editable in the visual builder.
uids are deterministic (`md5('miraex-home-v1|<key>')`), so re-running a script
**overwrites** manual edits to that page/template. Edit the script or the builder —
not both.

## Gotchas learned while writing these

- `css_*` attributes are `{selector, style, val}`; `mfnuidelement` in the selector is
  replaced with the element's uid.
- `dimensions` fields with `'version' => 'separated-fields'` (padding, margin) take
  `['top'=>…, 'right'=>…]`; the ones without it (border-width, border-radius) take a
  single `"1px 1px 1px 1px"` string.
- `Mfn_Helper::mfnLocalStyle()` drops any value that is `empty()` — use `'0px'`, not `'0'`.
  `dim()` normalises a bare `'0'` for you; a plain (non-`dim`) value still has to carry a unit,
  so `flex-shrink: '0'` silently vanishes — use the `flex` shorthand instead (below).
- **A button's `size` is a `transform: scale()`, not a padding scale.** `.button_size_1/3/4`
  are `scale(0.9)/scale(1.1)/scale(1.2)`, so `size => '3'` rendered every button 10% larger
  than the font-size and padding actually set on it — and, because the layout box does not
  scale, a correct 14px row gap measured as 0. `size => '2'` is the unscaled one; the
  reference metrics are applied as plain CSS on top of it.
- `mfnLocalStyle` prefixes the `flex` property with `"0 0 "`, so `css( $sel, 'flex', '82%' )`
  emits `flex:0 0 82%`. Handy for rail items, useless for anything else — set
  `flex-grow`/`flex-shrink`/`flex-basis` individually when you need a real shorthand.
- **The selector rewriter renames builder class tokens.** `section_wrapper` →
  `mcb-section-inner-<uid>`, `mcb-wrap-inner` → `mcb-wrap-inner-<uid>`, `mcb-column-inner` →
  `mcb-column-inner-<uid>`. A custom selector containing a literal `.mcb-column-inner` gets
  the *section's* uid appended and matches nothing. Target `.wrap` (untouched) or override
  the `--mfn-column-gap-*` variables the margins are built from instead.
- `quick_fact`'s `number` field runs through the counter animation, which rounds; put
  non-integer values such as `2.4M` in `heading` instead.
- Responsive visibility classes: BeTheme's breakpoints are desktop **1441px+**, laptop
  960–1440, tablet 768–959, mobile <768. To hide something above 960px you need
  `hide-desktop hide-laptop`, not `hide-desktop` alone.
- Wrap widths: wraps have no max-width field — use `css_advanced_flex` (style `width`)
  and always give it `tablet`/`mobile` values, or the fixed px width leaks to small screens.
- **`css_advanced_flex` needs `width_switcher => 'custom'` next to it.** BeBuilder only shows
  the numeric width field when Width = Custom, and the value is conditional on that switch.
  Setting the value alone renders correctly but the UI reads "Default", and a save from the
  builder then drops the width. `wrap()` in `lib.php` now derives the switch from the value,
  and `audit_widths.php` checks every wrap and item for the mismatch.
- The child theme's `miraex.css` styles the generic class `.section`, which BeBuilder
  sections also use; its padding rules are scoped with `:not(.mcb-section)` for that reason.
  BeTheme *also* prints its own bare-`.section` wrappers — `page.php` always emits an empty
  `<section class="section section-page-footer">` for `wp_link_pages()` — so those rules
  additionally exclude `[class*="section-page-"]`, and the child `style.css` hides the empty
  wrapper outright (`.section-page-footer:not(:has(.pager-single))`). Left alone it was
  **256px of dead space** above the footer on the front page.
- `gap` on a wrap container sets **row-gap too**. On a single-line grid (the footer's four
  columns) that adds a phantom 40px of height to the wrapper — use `column-gap`.
- Two items sit on the same line only if both are `width_switcher => 'inline'`
  (the footer logo + "a SEALSQ company" lockup).
- `card_attr()` carries `align-self:stretch` + `height:100%` for equal-height card grids.
  On a **full-width row** that forced height swallows the row's own bottom margin, so the
  rows end up touching — unset both (see the Root-to-Qubit rows).
- The `flex` shorthand is unusable: `mfnLocalStyle()` prefixes it with `"0 0 "`. Use
  `flex-grow` / `flex-shrink` / `flex-basis` separately (the stack rows need
  `54px | 1fr | auto`).
- BeBuilder nests **section → wrap → item** and nothing deeper. A card that has to sit
  *inside* a half-width column must therefore be an **item** whose frame is drawn on its
  own `.mcb-column-inner` (see `app_items()` / `card_item()`), not a wrap — a wrap would
  become a sibling column and break out of the split.
- **BeTheme elements come with their own geometry and hover effects — clearing a colour
  field in the UI is not enough.** `list` ships an 80x80 `.list_left` tile plus
  `.list_right{margin-left:100px}`; `icon_box` with `icon_position:left` reserves 145px and
  a 126px round wrapper; and the theme's options CSS paints the accent colour on
  `.icon_box:hover .icon_wrapper:before` and scales/nudges the tile and glyph on hover.
  Those are theme rules, so an empty field just falls back to them — they have to be
  overridden explicitly (see `tick_items()` and `feature_card()`).
- Sizing an `icon_box` tile takes **three** properties, not one. BeTheme's
  `.icon_box .icon_wrapper` is `font-size:50px` with a 110px box; setting only
  `width`/`height` leaves the glyph resting at the bottom, because `.icon`
  (`display:block`) inherits the 50px line box. `line-height:1` does not help either —
  it resolves against that same 50px font-size. Set **width + height + font-size +
  line-height:1** together (`feature_card()`, the homepage cards, the mega menu).
- Cards in `app.css` are **left aligned** (`.card .ico` has no auto margin,
  `.desc_wrapper` is not centred). `icon_box` centres everything by default, so the icon
  margin and `.desc_wrapper{text-align}` both need setting.
- BeTheme's `timeline` element only renders the alternating zig-zag layout, which does not
  fit a half column; `timeline_items()` rebuilds the reference's single-column shape.
- Something in the CF7/BeTheme stack dims `input.wpcf7-submit` to `opacity:.3`; the contact
  page overrides it explicitly.
- Long nested calls: `section( …, array_merge( [ … ], feature_cards( … ) ) )` needs **four**
  closing parens. Run `php -l` before every build — this bit twice.

### One content edge

Everything lines up on a single content edge, and **no section declares its own container
width** — they all inherit BeTheme's content width (1220px), so changing it in Theme Options
moves the whole site together. What has to be said explicitly is only what BeTheme insets
differently:

- Page content sits 12px inside the wrapper, because `.mcb-column-inner` carries
  `--mfn-column-gap-left/right: 12px`. Anything that is **not** a column has to reproduce
  that 12px or it hangs outside the edge: the header button (a column whose margins
  `button_item()` zeroes for the 14px row gap) and the footer logo (whose margin was set for
  the co-brand gap).
- A **wrap** already gets the same 12px, but as padding on `.mcb-wrap`, so the frame — which
  is drawn on `.mcb-wrap-inner` — is on the content edge with no help. Adding a margin there
  counts the gutter twice and pushes the frame 12px in (this happened to the CTA band and is
  why it looked narrower than the news cards, whose frames are also `.mcb-wrap-inner`).
- `.mfn-header-tmpl` overrides the gutter to 5px; the header sections set it back to 12px.
- The reference's two containers (`--container:1200px` and `--container-wide:1320px`, used by
  header/hero/footer) are **not** reproduced — one width is used throughout. The footer used
  to declare 1320px, which is what made it hang outside every other section.

### Phone layout

- The side gutter is **15px** and lives on `.section_wrapper` (`section()` sets it for every
  section). BeTheme's own 33px on that element is what made it ~53px before. Sections
  therefore carry **no** horizontal padding on mobile — `section_pad()` emits `0px` sides.
- Header and footer templates need different handling: be.css forces
  `.mfn-header-tmpl .section_wrapper` padding to `0 !important` (so the header bar keeps the
  15px on the section itself) and pads `.mfn-footer-tmpl .mcb-section` by 33px with a
  specificity the generated rule cannot beat (so the footer sections pass `0px !important`).
  Both also reset `--mfn-column-gap-left/right` to `0px` on mobile, or the column margin
  (12px in the page, 5px in the header) stacks on top of the gutter.
- `.site-header .bar` is a fixed **74px** row with `justify-content:space-between`; the header
  sections reproduce both (`min-height` on the wrapper), otherwise the bar grows with the
  tallest item and the burger sits next to the logo instead of at the right edge.
### The section library

`build_section_templates.php` builds it. How a template behaves is decided **when it is
inserted**, not by the template itself ([`class-mfn-builder-ajax.php::_template`](../../../betheme/functions/builder/class-mfn-builder-ajax.php)):

- plain insert → the items are copied into the page and `Mfn_Builder_Helper::unique_ID_reset()`
  gives them fresh uids: an independent preset. Because the uids differ from the generated
  ones, re-running a page builder cannot collide with it — but it *does* overwrite the whole
  page, so anything hand-dropped onto a generated page still disappears.
- global insert → the page stores only `mfn_global_section_id`, and the content is read from
  the template on every render (`class-mfn-builder-front.php`), with the stylesheet loaded from
  `post-<template-id>.css`. Editing the template changes every page that uses it.

BeTheme's own **Pre-built sections** panel is a *remote* library (`_pre_built_section` calls
Muffin's API) — our sections cannot be added to it, they live under Templates.

uids in a template are re-derived from its slug (`tpl-<slug>-…`), so re-running the script is
idempotent and never produces the same uid as the page it was read from — verified: 0
collisions, and an inserted copy measures identically to the source section.

- `mobile_rail()` turns a section's wraps into a horizontal snap-scrolling rail below 768px
  (used by the homepage news cards: ~1700px stacked → 588px). It is CSS only, so the desktop
  grid is untouched — but the section must contain *nothing but* the cards, which is why the
  news head and the news cards are two sections.

## Matching the reference pixel values

The reference stylesheet was measured in a browser and these are the values the
builders reproduce (checked with a DOM probe at a 1440px viewport):

| Token | app.css | value at 1440px |
| --- | --- | --- |
| home hero H1 | `clamp(2.7rem,6.2vw,5.2rem)` + `letter-spacing:-.035em` | **83px**, 4 lines in a 760px column |
| page hero H1 | `clamp(2.3rem,5vw,3.8rem)` | **61px** |
| section H2 | `.h2{clamp(2rem,3.6vw,3rem)}` line-height 1.08 | **48px** |
| lead | `.lead{clamp(1.05rem,1.5vw,1.3rem)}` line-height 1.55 | **20.8px** |
| button | `.btn{padding:14px 26px;font-size:15px}` + inherited `line-height:1.65` | 54.8px tall |
| large button | `.btn-lg{padding:17px 32px;font-size:16px}` | hero / CTA only, 62.4px tall |
| button row | `.btn-row{display:flex;gap:14px;flex-wrap:wrap}` | 14px on both axes |
| button arrow | `.btn svg{width:18px;height:18px}`, `.btn{gap:10px}` | **only** `.btn-primary` and standalone ghost links carry it |
| card | `.card{padding:30px;radius:14px}` · `.ico{52px;radius:13px;mb:20px}` | — |
| grid | `.grid{gap:24px}` | 12px padding per side of the outer wrap |
| section head | `.section-head{max-width:760px;margin-bottom:48px}` | wrap width 784px (760 + 2×12px column margin) |
| split | `.split{gap:64px}` | 40px wrap padding + 2×12px column margins |
| CTA band | `.cta-band{padding:72px 56px}` · `.inner{max-width:680px}` | full container width (1196); the 680px inner comes from **246px side padding**, not a max-width on the columns — capping the columns lets a button ride up next to the lead instead of each 1/1 column taking its own flex line |
| stack | `.stack{gap:12px}` · `.stack-layer{padding:20px 24px}` | — |
| spec table | `td{padding:15px 20px}` · first col 45% · value in mono 14px | — |
| stat card | `.stat{padding:28px;border:1px solid var(--line);radius:14px;background:rgba(255,255,255,.02)}` | grid gap 24px |
| stat value | `.stat b{clamp(2rem,3.4vw,2.9rem)}` + `background:var(--grad-spectral);background-clip:text;color:transparent` | **46.4px**, gradient-clipped |
| stat caption | `.stat span{13.5px;var(--f-mono);letter-spacing:.03em}` | mono, not Inter |
| hero metric | `.hero-meta .m b{30px}` white · `span{13px mono letter-spacing:.05em}` | different from `.stat` |
| logo strip | `.logos{display:flex;flex-wrap:wrap;justify-content:center;gap:18px 40px;opacity:.9}` | not a grid |
| logo pill | `.lg.mark{padding:8px 14px;border:1px solid var(--line);radius:999px}` · `img{max-height:34px;max-width:132px}` | — |
| news thumb | `.imgcard .ph{aspect-ratio:16/10}` | 238px at a 382px card |
| footer | `.site-footer{padding-block:96px 32px}` · `.foot-grid{grid:1.6fr 1fr 1fr 1fr;gap:40px;padding-bottom:48px}` → **400/250/250/250** · `h5{margin-bottom:18px}` · `a{padding:6px 0}` · `.foot-about p{max-width:300px;margin:16px 0 20px}` · `.brand-full .logo-img{height:46px}` · `.foot-bottom{padding-top:26px}` | `.container.wide` = 1320px |
| news card | `.imgcard{radius:14px;overflow:hidden}` · `.bd{padding:24px}` · `.date{mono 12px ls .05em}` · `h3{1.15rem/1.25}` · `p{14.5px}` · `.more{mono 12.5px}` | image flush to the frame: zero the column's 12px side margins |

**Vertical rhythm.** BeTheme sets `--mfn-column-gap-bottom: 40px` on every item
column, which is far looser than the reference (18px after an eyebrow or heading,
16px after a paragraph). `col_margin()` in `lib.php` states the rhythm explicitly and
every helper applies it, so do the same for any new element instead of relying on the
theme default.

**Build each component once.** `news_card()` existed twice — inline in `build_home.php` and
again in `build_pages.php` — and the two drifted apart, so a fix landed on one and not the
other. Every shared component now lives in `lib_page.php` and `build_home.php` requires it.

**Read the reference rule before assuming.** Two components looked like they should be
plain and turned out to be the opposite: `.stat` really is a bordered card with a
gradient-clipped value and a mono caption, and every logo in `.logos` really is a
bordered pill. Grep `app.css` for the class first — guessing from a screenshot cost a
round trip on both.

**Reuse the child theme's CSS where it already has the reference rule.** `miraex.css`
carries the whole scoped design layer, including things the builder cannot express — e.g.
`.scroll-cue` + `@keyframes cue` for the animated hero dot. Rendering the reference markup
(`<div class="scroll-cue"><span>Scroll</span><span class="dot"></span></div>`) inside a
`plain_text` item is both shorter and more faithful than restyling it with `css_*` attrs,
which cannot declare keyframes at all. Two caveats: the stylesheet only loads on the front
page / `miraex-home` (`miraex_should_enqueue()`), and BeBuilder adds positioned ancestors —
`.mcb-column-inner` and `.mcb-wrap-inner` are both `position:relative`, so an absolutely
positioned element needs them set to `static` to anchor to the section instead of its column.

**Hover states** reproduced from `app.css`: `.card`/`.imgcard` lift
(`translateY(-4px)` + cyan border + `0 18px 50px -20px rgba(0,0,0,.7)`),
`.imgcard .ph img` `scale(1.05)`, `.app` tinted background, `.stack-layer`
`translateX(6px)`, `.spec tr` tinted row, logo `opacity .72 → 1`, accordion question,
buttons, links and breadcrumbs. They are plain `:hover` attrs on the element's own
selector, so they survive in the builder UI.

## Attaching the mega menu

`build_megamenu.php` only creates the template. BeTheme links it per menu item:

**Appearance → Menus → Miraex Main Menu → expand "Solutions" → "Mega menu" → pick
"Mega menu — Solutions"**, then Save. That writes `mfn_menu_item_megamenu = <template id>`
on the menu item; the walker in `visual-builder/classes/header-template-items-class.php`
only treats it as a template when the value is **numeric** (the value `enabled` means
BeTheme's older automatic mega menu, built from the submenu items).

Panel geometry does not come from the builder content — BeTheme wraps the template in
`#mfn-megamenu-<id>` and styles it from the template's own metas:

- `megamenu_width = custom-width`, `megamenu_custom_width = 620px`, `megamenu_custom_position = left`
- `mfn-page-options-style` — a `[selector => [property => value]]` map printed by
  `MfnMegaMenu::css()`, with `postid` replaced by the template id at render time. Properties
  without an options field (`backdrop-filter`, `box-shadow`) can simply be added to the map.
- BeTheme also wraps the content in `.mfn-megamenu-tmpl-builder` with `24px` padding and
  `max-width:1200px`; both are zeroed there so the panel owns its 16px padding.

`icon_box` with `icon_position: left` is laid out for a 126px round icon
(`padding-left:145px; min-height:126px`, `.icon_wrapper{width:110px;border-width:8px;border-radius:100%}`
plus a stripes background image and an inset shadow). Reproducing the reference's 38px
square tile means overriding all of that — see the item attrs in `build_megamenu.php`.
