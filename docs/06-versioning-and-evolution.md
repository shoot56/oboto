## Versioning (current state)

- **Theme version**: maintained in `style.css` (`Version: 1.1.9`).
- **Update channel**: theme includes GitHub Updater headers (`GitHub Theme URI`, `Primary Branch`) and sets `gu_ignore_dot_org` to true in `functions.php`.
  - **TODO: Clarify with tech lead**: the exact release process used (tags? zip builds? direct main branch deploy?).

## Content model evolution

- **Learning Center rewrites**
  - When changing CPT/taxonomy rewrite rules, bump the `$rewrite_version` string in `functions.php` (Learning Center rewrite flush block).
  - This triggers a one-time `flush_rewrite_rules(false)` and stores the new version in `oboto_learning_center_rewrite_flushed`.
  - A fallback taxonomy rewrite is also inserted via `option_rewrite_rules`; changes there should be treated as contract-level routing changes.

## Safe extension guidelines (practical)

- **Adding a new block**
  - Follow the existing `blocks/<slug>/` structure (`block.json`, `block.php`, `block-render.php`).
  - Reference registered asset handles from `block.json` rather than enqueueing ad hoc.
  - Keep front-end breaking changes to markup/classes minimal; many scripts rely on specific selectors/classes.
- **Changing AJAX behaviors**
  - Treat `action` names as an external contract with the JS layer. Avoid renaming without updating all `view-script.js` callers.
  - Keep response shape stable (`wp_send_json_success($html)` returning HTML fragments), unless you migrate the JS accordingly.
- **Changing navigation/menu fields**
  - `Header_Menu_Walker` reads ACF fields on menu items; changes to those field names/types are breaking.
  - Keep ACF JSON (`acf-json/`) in sync across environments.

## Changelog / migrations

- No changelog or migration mechanism is present in this theme repo.
  - **SHOULD**: when making routing or content-model changes, document them in the pull request and bump `style.css` version.
