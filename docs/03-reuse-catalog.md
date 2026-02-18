## Purpose

This file is a catalog of **existing reusable building blocks** in this theme. Prefer reusing these before adding new one-off implementations.

## Theme-level reusable modules (`inc/`)

- **`inc/helpers.php`**
  - **When to use**: Always; it is the block registration backbone.
  - **Provides**
    - Registers all blocks by scanning `blocks/*` and calling `register_block_type($blockDir)` + `require $blockDir.'/block.php'`.
    - Registers a custom block category `theme_block_category`.
    - Registers a pattern category `theme_pattern_category`.
    - `dd(mixed $data, string $label = '', bool $return = false)` debug helper (prints styled dump and terminates).
- **`inc/custom-post-type.php`**
  - **When to use**: Extending the Learning Center content model (CPT/taxonomy) or its rewrites/rest settings.
  - **Provides**: `learning-center` CPT and `learning-center-category` taxonomy.
- **`inc/navigations-functions.php`**
  - **When to use**: Rendering navigation menus with ACF-enhanced menu items.
  - **Provides**: `Header_Menu_Walker` (custom `Walker_Nav_Menu`) supporting ACF fields like `icon`, `submenu_type`, `item_type`, `open_in_new_tab`.
- **`inc/class-alison-contact-forms.php`**
  - **When to use**: Enforcing corporate email-only submissions on Contact Form 7 forms.
  - **Provides**: `Alison_Contact_Forms_Handler` (blocked domains list + typo detection with Levenshtein).

## Theme settings (ACF options)

- **Options page**: `theme-general-settings` (created in `functions.php`)
  - **When to use**: Global header/footer CTAs or similar site-wide configurable content.
  - **Known fields (from `acf-json/group_679915aae0db5.json`)**
    - `header_button` (ACF Link field; used by `blocks/navigation/block-render.php`)
    - `cta_1`, `cta_2`, `cta_3` (ACF WYSIWYG fields)

## Reusable blocks (`blocks/*`)

> Use blocks by inserting them in the Site Editor or templates (e.g. `templates/*.html`). Each block’s canonical implementation lives in its folder under `blocks/<slug>/`.

### Navigation / layout

- **`oboto/navigation`** — Navigation block.
  - **When to use**: Header and footer menus (see `parts/header.html`, `parts/footer.html`).

### Learning Center

- **`oboto/learning-center-archive`** — Archive block for Learning Center with category filter and AJAX load more.
  - **When to use**: `archive-learning-center.html` and `taxonomy-learning-center-category.html`.
- **`oboto/learning-center-single`** — Single post block for Learning Center with related posts sidebar.
  - **When to use**: `single-learning-center.html`.
- **`oboto/learning-centre-list`** — Learning Centre block.
  - **When to use**: On pages where a “Learning Centre” listing/section is required.
  - **TODO: Clarify with tech lead**: intended difference between `learning-centre` (British spelling) and `learning-center` blocks.

### Blog / posts

- **`oboto/blog-list`** — Blog List block.
  - **When to use**: Blog listing experiences (some variants use AJAX filtering/load-more).
- **`oboto/posts-list`** — Posts List block.
  - **When to use**: Generic listing of posts (implementation details in `blocks/posts/*`).
- **`oboto/latest-posts`** — Latest Posts block.
  - **When to use**: “Latest” feed section.
- **`oboto/author-blog`** — Author Blog List block.
  - **When to use**: Author-focused listings.
- **`oboto/post-head`** — Post Head block.
  - **When to use**: Post header/hero section for a single post view.
- **`oboto/releated-posts`** — Releated Posts block.
  - **When to use**: Related content sections.

### Content sections / components

- **`obot/hero`** — Hero block.
  - **When to use**: Primary hero sections on landing pages.
- **`oboto/hero-questions`** — Hero Chat block.
  - **When to use**: A hero section with question/chat UI.
- **`obot/banner`** — Banner block.
  - **When to use**: Page banners and announcements.
- **`oboto/tools-grid`** — Tools Grid block.
  - **When to use**: Grid presentation of tools/items.
- **`oboto/tools-tabs`** — Tools Tabs block.
  - **When to use**: Tabbed tool/item presentation.
- **`obot/cards`** — Cards block.
  - **When to use**: Card grids/lists.
- **`oboto/mcp-list`** — MCP List block.
  - **When to use**: Catalog of MCP servers (from [obot-platform/mcp-catalog](https://github.com/obot-platform/mcp-catalog)) with search and category filters. Supports manual (ACF repeater) or automatic (GitHub API with transient cache) data source.
- **`obot/steps`** — Steps block.
  - **When to use**: Step-by-step sections.
- **`obot/work-steps`** — work-steps block.
  - **When to use**: Work process steps (variant of steps).
- **`oboto/media-text`** — Media Text block.
  - **When to use**: Media + text split layouts.
- **`obot/image-tabs`** — Image tabs block.
  - **When to use**: Image-based tabbed UI.
- **`oboto/cta`** — Form CTA block.
  - **When to use**: Call-to-action section that includes a form (as implemented).
- **`oboto/cta-content`** — CTA Content block.
  - **When to use**: CTA content sections without the form variant.
- **`obot/button`** — button block.
  - **When to use**: Consistent button markup/styling where a block is required.
- **`obot/quote`** — quote block.
  - **When to use**: Quote/testimonial sections.
- **`oboto/custom-video`** — Custom Video block.
  - **When to use**: Video embeds with theme styling.
- **`oboto/faqs`** — Faqs block.
  - **When to use**: FAQ sections.
- **`oboto/oboto-faqs`** — Oboto Faqs block.
  - **When to use**: A second FAQ variant; see `blocks/oboto-faqs/*`.
  - **TODO: Clarify with tech lead**: intended difference vs `oboto/faqs`.
- **`obot/glossary-item`** — Glossary item block (accordion row with InnerBlocks content).
  - **When to use**: Glossary/definition lists where each item expands to reveal rich content.
- **`oboto/footer-get-started`** — Footer Get Started block.
  - **When to use**: Footer “get started” CTA section.
- **`oboto/not-found`** — 404 Block.
  - **When to use**: Not-found/empty-state UI.

## Reusable patterns (`patterns/`)

- **`theme_pattern_category/hero-code`** (`patterns/hero-code.php`)
  - **When to use**: Hero pattern with a code snippet callout.
  - **Note**: Contains a `docker run ...` snippet in markup (content, not executable code).
- **`theme_pattern_category/plams`** (`patterns/plans.php`)
  - **When to use**: Pricing/plan comparison layout.
  - **Note**: The slug is `plams` (typo) in the file header.
- **`theme_pattern_category/404`** (`patterns/hidden-404.php`)
  - **When to use**: A 404 pattern that includes a search block.

## Global frontend utilities

- **`js/script.js`**
  - **When to use**: Site-wide UX behaviors already present (AOS init, smooth scroll, code copy/highlighting, navigation submenu toggles).
  - **Caution**: Avoid duplicating similar behaviors in block scripts if already handled globally.
