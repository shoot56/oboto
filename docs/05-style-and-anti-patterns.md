## Style rules (extracted from existing code)

### PHP / WordPress

- **Prefer WordPress hooks** for lifecycle actions:
  - `after_setup_theme`, `wp_enqueue_scripts`, `admin_enqueue_scripts`, `enqueue_block_editor_assets`, `init`, `template_redirect`.
- **Prefer `filemtime(...)`** for cache busting compiled assets (used throughout `functions.php` and block `block.php` files).
- **Sanitize request input** before using it:
  - Examples: `sanitize_text_field($_POST['category'])`, `sanitize_email($_POST[...])`, `intval($_POST['paged'])`.
- **Use WordPress APIs for content access**
  - `WP_Query`, `get_terms`, `wp_get_post_terms`, `get_permalink`, `get_the_excerpt`, etc.
- **Blocks should render via `block-render.php`**
  - `block.json` sets `acf.renderTemplate` → PHP renders the HTML.
  - Use `get_block_wrapper_attributes(...)` for wrapper output.

### JavaScript

- **Scope JS to DOM ready** (`DOMContentLoaded` or `jQuery(document).ready(...)`).
- **Prefer class toggles** for UI state changes (e.g. menu open/close).
- **AJAX conventions in this repo**
  - Actions are posted to `admin-ajax.php` using a required `action` key.
  - Server replies with `wp_send_json_success(...)`; JS parses JSON and injects HTML.

### Documentation & comments

- **Comments must be in English** (enforced by project rules; keep it consistent).

## Prohibited patterns (do not introduce new occurrences)

- **No magic numbers**
  - Use named constants/config for things like page sizes instead of raw values (e.g. `posts_per_page => 12` exists today; treat as legacy).
- **No duplicate code when a reusable pattern exists**
  - Blocks follow a strict folder anatomy (`block.json` + `block.php` + `block-render.php`); do not create ad-hoc one-off render code outside this structure.
- **No non-English comments or inline docs**
  - Keep comments in English only.
- **No misleading / cryptic names**
  - Prefer explicit function names (block-prefixed function names match existing practice).
- **No bypassing the theme architecture**
  - Don’t register blocks or hooks in random locations; keep theme bootstrap in `functions.php` and modules in `inc/` / per-block `block.php`.
- **No hard-coded WordPress paths for AJAX**
  - Do not hardcode `"/wp-admin/admin-ajax.php"` in new JS (multiple scripts do this today; treat as legacy).
- **No direct output without escaping (unless intentionally outputting trusted HTML)**
  - Prefer `esc_html`, `esc_attr`, `esc_url` for output. If outputting HTML from a trusted source, document why.

## Legacy exceptions currently present (should not be copied)

- **Hardcoded AJAX URL**
  - Several scripts call `"/wp-admin/admin-ajax.php"` directly (e.g. `blocks/learning-center-archive/view-script.js`).
- **Hardcoded site URLs in content**
  - Example: `parts/footer.html` contains `http://oboto.local/...` image URL.
  - Treat these as content/environment artifacts; do not hardcode hostnames in theme code.
- **Console logging in production scripts**
  - `console.log(...)` exists in `js/script.js` and some block scripts.
- **Large blocked domain list embedded in PHP**
  - `inc/class-alison-contact-forms.php` includes a very large array of domains. Do not duplicate this pattern without a clear reason.
