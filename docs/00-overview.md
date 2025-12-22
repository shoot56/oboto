## Project summary

- **What this is**: A WordPress **block theme** (`theme.json`, `templates/*.html`, `parts/*.html`, `patterns/*.php`) named **`obot`** (`style.css`).
- **Goal (as implemented)**: Provide the site UI using custom blocks under `blocks/*` and theme templates/parts; includes a dedicated **Learning Center** content area via a custom post type and taxonomy.
- **Primary audiences**
  - **Content editors** using the WordPress Site Editor / block editor to build pages and manage content.
  - **Theme developers** extending blocks, templates, styles, and WordPress hooks in this theme.

## Tech stack

- **Backend/runtime**
  - **WordPress** (PHP) theme code (`functions.php`, `inc/*.php`, `blocks/*/*.php`).
  - **Custom Post Type + Taxonomy**: `learning-center` and `learning-center-category` (`inc/custom-post-type.php`).
  - **ACF (Advanced Custom Fields)** is used extensively:
    - `get_field(...)` in block renders and menu walker (`blocks/navigation/block-render.php`, `inc/navigations-functions.php`).
    - Options page via `acf_add_options_page(...)` (`functions.php`).
    - Local field group JSON in `acf-json/*.json`.
  - **Contact Form 7** integration via filters (`functions.php`, `inc/helpers.php`, `inc/class-alison-contact-forms.php`).
- **Frontend**
  - Vanilla JS and **jQuery** (theme script enqueued with `jquery` dependency).
  - Custom per-block scripts in `blocks/*/view-script.js` for interactivity and AJAX.
- **Third-party frontend libraries (CDN-loaded)**
  - Swiper, AOS, PrismJS, Highlight.js, GSAP + ScrollTrigger, Lottie (`functions.php`).

## Runtime environments

- **Runs inside WordPress** as a theme (executed by WP on every request via `functions.php`).
- **PHP version**: TODO: Clarify with tech lead (not specified in repo).
- **Build/tooling**: There is `scss/` and compiled `css/`, but **no package/build config** (no `package.json` / `composer.json` in this repo).
  - **TODO: Clarify with tech lead**: how SCSS is compiled into `css/*.css` in this project (CI step? local tooling? external repo?).

## Main entrypoints

- **Theme bootstrap**
  - `functions.php`: theme supports, assets, plugin-related filters, rewrite rules, and includes `inc/*`.
  - `inc/helpers.php`: registers all blocks under `blocks/*` and adds editor block styles.
- **Templates (FSE)**
  - `templates/*.html`: route-level layout (e.g. `archive-learning-center.html`, `single-learning-center.html`, `taxonomy-learning-center-category.html`).
  - `parts/header.html`, `parts/footer.html`: composed into templates via `wp:template-part`.
  - `patterns/*.php`: reusable block patterns registered under `theme_pattern_category`.
- **Blocks**
  - `blocks/*/block.json`: block metadata, including ACF render template (`acf.renderTemplate`) and declared `style`/`viewScript` handles.
  - `blocks/*/block.php`: registers block-specific scripts/styles and (sometimes) AJAX handlers.
  - `blocks/*/block-render.php`: PHP render templates (server-side output).
- **Frontend scripts**
  - `js/script.js`: global DOM behaviors (menu UX, smooth scroll, code copy/highlighting).
  - `js/block-styles.js`: editor-only block style registration for core blocks.

## High-level data flow

### Page request → render

- **HTTP request** → WordPress resolves route → selects an FSE template (`templates/*.html`).
- Template composes **template parts** (`parts/header.html`, `parts/footer.html`) and custom blocks (e.g. `wp:oboto/learning-center-archive`).
- Custom blocks render via PHP templates (`blocks/*/block-render.php`), commonly reading ACF fields via `get_field(...)` and querying WP content via `WP_Query`.

### AJAX request → content fragments

- Some blocks attach client scripts (e.g. Learning Center archive, blog list) that call WordPress AJAX endpoints.
- Client JS posts to `admin-ajax.php` with an `action` value.
- Server handler (registered via `add_action('wp_ajax_*', ...)` and `wp_ajax_nopriv_*`) executes WP queries and returns HTML fragments via `wp_send_json_success(...)`.

## Repository shape (monolith / modular / monorepo)

- **Single WordPress theme repo** (not a monorepo; no packages folder).
- Functionality is **modular by folder convention**:
  - `inc/` = theme-level PHP modules
  - `blocks/` = feature blocks (each in its own folder)
  - `templates/`, `parts/`, `patterns/` = FSE presentation structure
