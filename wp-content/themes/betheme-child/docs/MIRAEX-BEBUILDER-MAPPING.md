# Miraex → BeTheme Builder mapping

## Header
Use BeTheme Header Builder.
- Miraex logo
- Solutions dropdown/mega menu
- Technology
- Company
- News
- Resources
- Careers
- Talk to us

Do not import the original `.site-header` HTML/CSS.

## Homepage content
- Hero: HTML element + Miraex classes initially; later optionally split into native text/buttons.
- Expertise: native BeBuilder cards are recommended.
- Microwave → Optical: keep as HTML/CSS if the visual diagram is needed.
- Root-to-Qubit: keep the visualization as HTML/CSS initially.
- Stats: native BeBuilder columns/elements.
- Recognition logos: native BeBuilder image/logo grid.
- Latest News: preferably native WordPress/BeBuilder dynamic content after the News pages are created.
- CTA: native BeBuilder.
- Footer: BeTheme Footer Builder.

## First-pass class namespace

Wrap the page content in a BeBuilder Section/Wrap with custom class:

`miraex-page`

The child theme already adds `miraex-page` to the body on the front page / `miraex-home` page, so the stylesheet is isolated.

## Rapid bootstrap

Add a BeBuilder Shortcode element:

`[miraex_homepage]`

This is only a bridge to get the design on-screen quickly. It intentionally excludes Header/Footer.
