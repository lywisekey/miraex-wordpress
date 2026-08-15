# Betheme Child + Miraex integration

## What this package changes

This is the existing `betheme-child` theme updated with the Miraex website's content design layer.

### Included

- `assets/miraex/css/miraex.css`
  - Miraex design system, scoped under `.miraex-page`
  - Generic selectors from the source site are namespaced to avoid collisions with BeTheme.
  - Original Header/Footer CSS was intentionally excluded because Header Builder / Footer Builder should own those areas.
- `assets/miraex/js/miraex.js`
  - Scroll reveal
  - Animated counters
  - Footer year helper
  - Header/mobile-menu code was intentionally removed; BeTheme Builder owns the header.
- `assets/miraex/img/`
  - Local copy of the source site's images, logos and favicon.
- `partials/miraex-homepage.php`
  - Homepage content only; no header/footer.
- `docs/miraex-homepage-content.html`
  - Raw content fragment for manual section-by-section BeBuilder conversion.
- `functions.php`
  - Enqueues Miraex assets only on the front page or `/miraex-home/`.
  - Adds `.miraex-page` to the body.
  - Adds `[miraex_asset]` and `[miraex_homepage]` shortcodes.

## Recommended setup

1. Activate this child theme.
2. Keep BeTheme as the parent theme.
3. Build the Header entirely in BeTheme Header Builder.
4. Build the Footer entirely in BeTheme Footer Builder.
5. Set the WordPress front page to the Miraex page, or create a page with slug `miraex-home`.
6. Add the body/page class `miraex-page` only through the child theme logic; do not add it globally to every page.
7. For a fast first pass, add a BeBuilder Shortcode element containing:
   `[miraex_homepage]`
   This renders the source homepage content while BeTheme controls Header/Footer.
8. After the visual baseline is approved, convert individual sections to native BeBuilder elements. Keep complex visual/technical sections as HTML elements using the supplied scoped classes.

## Asset shortcode

Inside a BeBuilder HTML/Shortcode element:

`[miraex_asset path="img/hero-photonics.jpg"]`

This returns the correct child-theme asset URL.

## Important

Do not edit the parent `betheme` directory.

The source site's original HTML/CSS/JS is not copied wholesale into the child theme. The CSS is scoped and the JavaScript has been reduced to page-content behavior so it does not fight BeTheme Header/Footer Builder.

The source HTML links are resolved through `miraex_page_url()` when the matching WordPress page exists; otherwise they fall back to clean `/{slug}/` URLs.
