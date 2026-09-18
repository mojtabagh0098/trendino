# Trendino — Premium RTL WooCommerce Theme

A production WordPress theme (not an HTML template) for Persian/RTL e-commerce,
built around **Elementor custom widgets** and **WooCommerce** data.

## Requirements

| | |
|---|---|
| WordPress | 6.0+ |
| PHP | 7.4+ |
| Elementor | 3.5+ (free is enough; Pro unlocks Theme Builder header/footer) |
| WooCommerce | 7.0+ |

## Architecture

```
trendino/
├─ functions.php            # constants + inc/ loader
├─ style.css                # theme header only (no styling)
├─ inc/
│  ├─ helpers.php           # env checks, sanitizers, Persian digits, SVG icon API
│  ├─ setup.php             # theme supports, menus, image sizes, sidebars
│  ├─ theme-options.php     # design tokens → CSS custom properties
│  ├─ enqueue.php           # conditional per-component asset loading
│  ├─ template-tags.php     # menu walker, post card, pagination, breadcrumbs
│  ├─ hooks.php             # body classes, AJAX (search/newsletter/wishlist), fragments
│  ├─ customizer.php        # Global Settings panel (single option array)
│  ├─ elementor.php         # boots the Elementor integration
│  ├─ woocommerce.php       # shop layout, columns, hooks (minimal overrides)
│  └─ tgm-plugins.php       # recommended-plugin admin notice
├─ elementor/
│  ├─ managers/             # widget + panel-category registration
│  ├─ helpers/              # Query_Helper, Render_Helper
│  ├─ controls/             # reusable control traits
│  └─ widgets/              # 28 custom widgets
├─ template-parts/          # header / footer / content partials
├─ woocommerce/             # the single required template override
├─ assets/{css,js,fonts,images}
└─ languages/
```

## Elementor widgets

**Header** — Logo · Search · Actions (account/cart/wishlist/compare) · Main Menu ·
Category Mega Menu · Top Bar · Mobile (drawer + bottom nav)

**Homepage** — Stories · Hero Slider · Banner · Banner Grid · Category Cards ·
Product Carousel · Product Grid · Product Tabs · Category Showcase · Deal Countdown ·
Brands · Service Features · Blog Posts · Newsletter · Section Heading · SEO Text

**Footer** — About · Menu · Social · Trust Badges · Bottom Bar

Every widget has Content / Layout / Style / Responsive controls and pulls its data
from WooCommerce or WordPress — nothing is hardcoded.

## Design tokens

All colours and metrics are CSS custom properties written once from the
Customizer (`trendino_root_css_variables()`), never repeated per file:

```
--trendino-primary       --trendino-text        --trendino-container
--trendino-primary-dark  --trendino-muted       --trendino-font-size
--trendino-primary-soft  --trendino-border      --trendino-radius
--trendino-secondary     --trendino-background  --trendino-card-radius
--trendino-accent        --trendino-surface
--trendino-success       --trendino-footer-bg
--trendino-warning       --trendino-footer-text
```

## RTL

The stylesheets use CSS **logical properties** (`inline-start/end`, `margin-inline`,
`padding-inline`) throughout, so a single build serves LTR and RTL — no `-rtl.css`
duplicates to keep in sync. Carousels detect direction at runtime and normalise
`scrollLeft`, so arrows and swipe work correctly in both.

## Performance

* Per-component CSS/JS, enqueued only when a widget renders (`trendino_enqueue_component()`).
* No jQuery, no slider library — the carousel is native scroll-snap (~6 KB).
* `loading="lazy"` / `decoding="async"` on every non-critical image.
* Responsive `<picture>` sources on banners and hero slides.

## Security

Output escaped at print time, input sanitized, nonces + `check_ajax_referer()` on
every AJAX endpoint, capability checks on admin actions, and WP_Query/`wc_get_products()`
instead of raw SQL.

## Fonts

Drop the Vazirmatn `.woff2` files into `assets/fonts/` to self-host them
(see `assets/fonts/fonts.css`). Without them the stack falls back gracefully.
