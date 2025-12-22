## Repository conventions (observed)

### Folder structure

- **Theme root**
  - `functions.php` = global bootstrap (supports, enqueue, filters, includes).
  - `theme.json` = block theme settings (palette, typography, layout sizes, etc).
  - `templates/*.html` = FSE templates (route-level page structure).
  - `parts/*.html` = template parts (header/footer).
  - `patterns/*.php` = block patterns registered under `theme_pattern_category/*`.
  - `inc/*.php` = theme-level PHP modules.
  - `blocks/*/` = custom blocks (one folder per block).
  - `scss/` (source) and `css/` (compiled output).

### Block implementation pattern (custom blocks)

Each block lives at `blocks/<block-slug>/` and typically contains:

- `block.json`
  - `name` follows `oboto/<slug>` or `obot/<slug>` (note: both prefixes exist).
  - `acf.renderTemplate` points to `block-render.php`.
  - `style` references a **registered style handle** (e.g. `"learning-center"`).
  - `viewScript` references a **registered script handle** (e.g. `"learning-center-archive-script"`).
  - `example.attributes.data.preview_image_help` is used for editor previews (points at `screenshot.png` when present).
- `block.php`
  - registers block assets (via `wp_register_style`, `wp_register_script`)
  - attaches WP hooks (some blocks define AJAX handlers here).
- `block-render.php`
  - server-side markup; usually starts with:
    - `$id` from `block['id']` or `block['anchor']`
    - `$wrapper_attributes = get_block_wrapper_attributes(['class' => ...])`
    - preview image fallback when `preview_image_help` is present
  - uses WordPress APIs (`WP_Query`, `get_terms`, `get_permalink`, etc)
  - uses ACF fields via `get_field(...)` for block configuration.
- `view-script.js` (optional)
  - implements frontend behavior; common patterns:
    - `DOMContentLoaded` or `jQuery(document).ready(...)`
    - class toggling for UI state
    - “load more” pagination counters
    - AJAX calls to `admin-ajax.php` with `action=...`

### PHP style conventions

- **Naming**
  - Functions are mostly `snake_case` (e.g. `theme_setup`, `theme_scripts`, `custom_blog_permalink`).
  - Block-scoped functions tend to be prefixed by the block name (e.g. `learning_center_archive_scripts`).
  - Classes use `PascalCase` (e.g. `Header_Menu_Walker`, `Alison_Contact_Forms_Handler`).
- **Hooks**
  - Hook registration is direct and local to the module (`add_action`, `add_filter`), often right after function definitions.
- **Sanitization / escaping (mixed, but present)**
  - Input sanitization is used for AJAX inputs (`sanitize_text_field`, `sanitize_email`) and type coercion (`intval`).
  - Output escaping is used in places (`esc_attr`, `esc_url`, `esc_html`) but not consistently across all markup.
  - **Rule of thumb for new code**: sanitize all request inputs and escape all output (attributes, URLs, HTML text).
- **Short echo tags**
  - `<?= ... ?>` is used frequently in render templates.

### JavaScript conventions

- **Global script**: `js/script.js`
  - uses AOS + Prism + highlight.js for animation and code highlighting.
  - uses jQuery for smooth-scrolling.
  - implements navigation submenu toggles and a “sticky header” behavior.
- **Per-block scripts**: `blocks/*/view-script.js`
  - some scripts use **hard-coded** `"/wp-admin/admin-ajax.php"` for AJAX.
    - **Rule of thumb for new code**: avoid hardcoding; instead pass the AJAX URL from PHP (e.g. via localized data) so it works under subdirectory installs and nonstandard admin paths.

### Assets / cache busting

- Theme enqueues compiled CSS with `filemtime(...)` for cache busting (`functions.php`, block `block.php` files).
- Editor-only block styles are registered via `enqueue_block_editor_assets` (`functions.php` → `js/block-styles.js`).

## Rules of thumb when adding new code (fit existing patterns)

- **New block**
  - Create `blocks/<slug>/{block.json,block.php,block-render.php}`.
  - Register any required CSS/JS handles in `block.php`, and reference those handles from `block.json` via `style` / `viewScript`.
  - Use `get_block_wrapper_attributes()` and support `anchor` in `block.json`.
  - For editor preview: add `"example.attributes.data.preview_image_help": "screenshot.png"` and include the image file.
- **New AJAX behavior**
  - Add `wp_ajax_*` + `wp_ajax_nopriv_*` handlers in the relevant `block.php`.
  - Sanitize inputs; build `WP_Query` with explicit `post_type`, `post_status`, `posts_per_page`.
  - Return via `wp_send_json_success(...)` and `wp_die()`.
- **New theme settings**
  - Add fields via ACF and ensure JSON sync in `acf-json/` is committed.
  - Read options via `get_field('<field>', 'option')`.

## TODO: Clarify with tech lead

- The canonical way to compile `scss/` into `css/` for this theme (no build config is present in this repo).
