## Integrations (directly referenced in code)

### WordPress plugins (theme assumes they exist)

- **Advanced Custom Fields (ACF)**
  - Evidence:
    - `acf_add_options_page(...)` in `functions.php`
    - `get_field(...)` usage in block renders and menu walker
    - ACF JSON sync directory: `acf-json/`
    - Block metadata uses `acf.renderTemplate` in `blocks/*/block.json`
  - Used for:
    - Block configuration fields
    - Theme options (`theme-general-settings`)
    - Menu item metadata (icons, submenu type, etc.)
- **Contact Form 7**
  - Evidence:
    - `add_filter('wpcf7_autop_or_not', '__return_false')` (`inc/helpers.php`)
    - `add_filter('wpcf7_skip_mail', ...)` (`functions.php`)
    - `add_filter('wpcf7_validate_email', ...)` (`inc/class-alison-contact-forms.php`)
  - Used for:
    - Newsletter/subscribe form in `parts/footer.html`
    - Email-domain validation and optional mail skipping
- **Yoast SEO**
  - Evidence:
    - `add_filter('wpseo_canonical', ...)` (`functions.php`)
  - Used for:
    - Canonical override on posts in category `blog`
- **WP All Import**
  - Evidence:
    - `wp_all_import_*` filters in `functions.php`
  - Used for:
    - Disabling stream filter and non-ascii stripping during CSV→XML conversion
- **GitHub Updater (theme updater)**
  - Evidence:
    - `GitHub Theme URI` and `Primary Branch` headers in `style.css`
    - `add_filter('gu_ignore_dot_org', '__return_true')` in `functions.php`

### Frontend libraries (loaded via CDN)

All of these are enqueued in `functions.php`:

- **Swiper** (`cdn.jsdelivr.net`) — CSS + JS
- **AOS** (`unpkg.com`) — CSS + JS
- **PrismJS** (`cdnjs.cloudflare.com`) — CSS + JS (core + autoloader)
- **Highlight.js** (`cdnjs.cloudflare.com`) — JS
- **GSAP + ScrollTrigger** (`cdn.jsdelivr.net`) — JS
- **Lottie player** (`unpkg.com`) — JS

### Social share endpoints (client-opened URLs)

- Learning Center single block includes share links opened in a popup:
  - Reddit, Facebook, LinkedIn, Twitter/X (`blocks/learning-center-single/block-render.php`)

## Configuration patterns

### ACF JSON sync

- Field groups live in `acf-json/*.json`.
- Expected workflow: update fields in WP admin → JSON sync updates files → commit.
  - **TODO: Clarify with tech lead**: whether ACF JSON sync is enforced (and how it’s deployed across environments).

### Theme options (WordPress options table)

- Learning Center rewrite flush uses a stored version key:
  - Option key: `oboto_learning_center_rewrite_flushed` (`functions.php`)
  - Value: string version like `lc_rewrite_v3_resources_learning_center_and_taxonomy`

### Environment variables

- No `.env`/config loader is present in this repo.
  - **TODO: Clarify with tech lead**: if any environment variables are required at the WordPress/app level (outside this theme).

## Logging / monitoring / feature flags

- **Logging**
  - Frontend: `console.log(...)` exists in `js/script.js` and some block scripts.
  - Backend: no explicit logging framework is present in this theme code.
- **Monitoring / feature flags**
  - Not implemented in this repo (no evidence).

## Integration diagram (theme ↔ external)

```mermaid
graph LR
  WP[WordPress] --> THEME[Theme: obot]
  THEME --> ACF[ACF plugin]
  THEME --> CF7[Contact Form 7]
  THEME --> YOAST[Yoast SEO]
  THEME --> WPAI[WP All Import]
  THEME --> GHUP[GitHub Updater]

  THEME --> CDN1[cdn.jsdelivr.net (Swiper, GSAP)]
  THEME --> CDN2[unpkg.com (AOS, Lottie)]
  THEME --> CDN3[cdnjs.cloudflare.com (Prism, Highlight.js)]
```
