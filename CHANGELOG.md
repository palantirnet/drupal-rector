# Changelog

All notable changes to **drupal-rector** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Historical entries (≤ 0.21.2) are reproduced from the
[GitHub Releases page](https://github.com/palantirnet/drupal-rector/releases) as
they were originally published; their format and level of detail varies
release-by-release.

## [Unreleased]

## [1.0.0-beta1] — 2026-06-11

### Added

- **`template_preprocess_*()` family (Drupal 11.3, [#3504125](https://www.drupal.org/node/3504125))** —
  extended `FunctionToServiceRector` coverage with 14 more `template_preprocess_*()`
  functions deprecated in drupal:11.3.0 and removed in drupal:12.0.0. Each is
  rewritten (BC-wrapped via `DeprecationHelper`) to the equivalent `*Preprocess`
  service method: `table`, `tablesort_indicator`, `item_list`, `region`,
  `maintenance_page`, `maintenance_task_list`, `install_page` → `ThemePreprocess`;
  `image` → `ImagePreprocess`; `breadcrumb` → `BreadcrumbPreprocess`;
  `pager` → `PagerPreprocess`; `field`, `field_multiple_value_form` →
  `FieldPreprocess`; `menu_local_task`, `menu_local_action` → `MenuPreprocess`.
  (`template_preprocess_authorize_report()` has no replacement and is
  intentionally excluded.)
- **`RemoveSourceModuleFromMigrateSourceAttributeRector`** — removes the
  `source_module` named argument from `#[MigrateSource]` attribute usages. Only
  the `Drupal\migrate\Attribute\MigrateSource` attribute is targeted (an
  attribute of the same short name from another namespace is left untouched).
  The `source_module` constructor parameter was removed from the attribute in
  drupal:11.2.0; passing `#[MigrateSource(source_module: '...')]` now raises an
  "Unknown named parameter" error at plugin discovery time. The rewrite cannot
  be BC-wrapped (an attribute is not an `Expr → Expr` transformation) and the
  argument is mutually exclusive across minors, so the rule lives in the opt-in
  `Drupal11SetList::DRUPAL_112_BREAKING` set, not the default deprecation set.
  For plugins extending `DrupalSqlBase` the `source_module` value must be
  re-declared via the `@MigrateSource` annotation or the migration YAML after
  removal — a manual follow-up this rule does not automate. Apply only after
  dropping support for Drupal minors that predate 11.2.0.
  [#3009349](https://www.drupal.org/i/3009349) /
  [change record](https://www.drupal.org/node/3306373)

- **`UploadedFileConstraintArrayOptionsToNamedArgsRector`** — replaces the
  deprecated options-array argument of `UploadedFileConstraint` with explicit
  named constructor arguments (e.g. `new UploadedFileConstraint(['maxSize' => 1024000])`
  → `new UploadedFileConstraint(maxSize: 1024000)`). Passing an options array is
  deprecated in drupal:11.4.0 and removed in drupal:12.0.0. The transformation is
  BC-wrapped: the named-argument constructor was introduced alongside the
  deprecation, so the new form would fatal (`Unknown named parameter`) on
  Drupal < 11.4. The rector therefore wraps the `new` expression in
  `DeprecationHelper::backwardsCompatibleCall()`, using named arguments on
  Drupal ≥ 11.4 and the original options array on older versions. See
  [#3561135](https://www.drupal.org/node/3561135) and the
  [change record](https://www.drupal.org/node/3554746).
- **`DRUPAL_114_BREAKING`: `HelpSearch` → `SearchHelpSearch` class rename.**
  `Drupal\help\Plugin\Search\HelpSearch` was moved out of the `help` module and
  renamed to `Drupal\search_help\Plugin\Search\SearchHelpSearch` in the new
  `search_help` core sub-module (`system_update_11400()`, drupal:11.4.0). Added
  as a `RenameClassRector` entry to `drupal-11.4-breaking.php`: the
  `SearchHelpSearch` class does not exist on any Drupal minor below 11.4, and a
  `use` / `::class` rename is structural and cannot be BC-wrapped, so applying
  it against code that still needs to run on an older minor would fatal there.
  Opt in via `Drupal11SetList::DRUPAL_114_BREAKING` only after dropping support
  for Drupal < 11.4 ([#3581109](https://www.drupal.org/i/3581109)).
- **`CommentLinkBuilderConstructorRector`** — rewrites the deprecated
  5-argument `new \Drupal\comment\CommentLinkBuilder(...)` constructor call to
  the new 3-argument form, dropping the `$module_handler` and
  `$entity_type_manager` arguments (deprecated in drupal:11.3.0, removed in
  drupal:12.0.0). Because the 3-argument signature only exists on Drupal >=
  11.3.0, the rewrite is BC-wrapped with `DeprecationHelper::backwardsCompatibleCall()`
  so the original 5-argument call still runs on older Drupal. Only calls with
  exactly 5 positional arguments are rewritten.
- **`ReplaceItemAttributesWithAttributesRector`** — replaces the deprecated
  `#item_attributes` key with `#attributes` in render arrays whose `#theme` is
  `image_formatter` or `responsive_image_formatter`. The `#item_attributes`
  property is deprecated in drupal:11.4.0 and removed in drupal:12.0.0. The
  transformation is BC-wrapped: the `#attributes` variable was only added to
  these theme hooks in 11.4.0, so a plain rename would silently drop the
  attributes on Drupal < 11.4. The rector therefore wraps the array literal in
  `DeprecationHelper::backwardsCompatibleCall()`, using `#attributes` on
  Drupal ≥ 11.4 and the original `#item_attributes` on older versions. Arrays
  with an unrelated (or absent) `#theme`, and arrays already using
  `#attributes`, are left untouched.
  [#3554447](https://www.drupal.org/i/3554447) /
  [CR](https://www.drupal.org/node/3554585).
- **`HookRequirementsAlterRenameRector`** — renames procedural
  `{module}_requirements_alter()` hook implementations to
  `{module}_runtime_requirements_alter()`, deprecated in drupal:11.3.0 and
  removed in drupal:13.0.0. The runtime hook is only invoked on Drupal minors
  where it exists, so the renamed function is a silent no-op on older Drupal;
  this is a non-BC rewrite and ships in the opt-in `DRUPAL_113_BREAKING` set, not
  the default deprecation set. The rule only renames functions with a single
  by-reference parameter, skips the `hook_requirements_alter()` API-doc function,
  and is idempotent — the `_runtime_`/`_update_requirements_alter()` hooks are
  left untouched.
- **`ReplaceDrupalStaticResetFileReferencesRector`** — rewrites
  `drupal_static_reset('file_get_file_references')` and
  `drupal_static_reset('file_get_file_references:field_columns')` to
  `\Drupal::service('cache.memory')->invalidateTags(['file_references'])`.
  Both static-cache keys were deprecated in drupal:11.4.0 (removed in
  drupal:13.0.0) when the file-reference lookup moved to the new
  `FileReferenceResolver` service, which uses the `file_references`
  memory-cache tag instead of `drupal_static()`. Only those two literal keys
  are matched; other `drupal_static_reset()` calls, calls to
  `file_get_file_references()` itself, and named/unpacked argument forms are
  intentionally left for manual review. BC-wrapped via `DeprecationHelper`:
  the `file_references` cache tag does not exist before drupal:11.4.0, so the
  new call would be a silent no-op there — the wrapper keeps the original
  `drupal_static_reset()` on older versions and only switches to the
  `cache.memory` invalidation on drupal:11.4.0 and above.
- **`ReplaceNodeViewControllerRector`** (+ a companion `RenameClassRector`
  entry) — migrates the deprecated
  `Drupal\node\Controller\NodeViewController` to
  `Drupal\Core\Entity\Controller\EntityViewController` (deprecated in
  drupal:11.4.0, removed in drupal:13.0.0). The custom rector rewrites
  `new NodeViewController($etm, $renderer, $currentUser, $entityRepository)`
  to `new EntityViewController($etm, $renderer)`, dropping the two extra
  constructor arguments that `EntityViewController` does not accept; it matches
  both the old and new class names so the argument trim is order-independent
  w.r.t. the rename pass. The `RenameClassRector` entry handles the structural
  references (`use` / `extends` / `::class` / type hints). Ships in the opt-in
  `DRUPAL_114_BREAKING` set: unlike the other entries there, the replacement
  class exists on every supported minor (so it never fatals on a missing
  symbol), but reparenting `extends NodeViewController` to
  `extends EntityViewController` is *behaviorally* breaking on every minor —
  subclasses lose the node-specific `create()` / `currentUser` /
  `entityRepository` / `title()` / `view()` members and can throw an
  `ArgumentCountError` or call an undefined `title()`, so it needs manual
  review. A subclass's own `parent::__construct($a, $b, $c, $d)` is not
  rewritten (PHP silently discards the extra arguments).
  [#3589630](https://www.drupal.org/i/3589630) /
  [CR](https://www.drupal.org/node/3589636).
- **`RenameHookRankingRector`** — renames the deprecated OOP hook attribute
  `#[Hook('ranking')]` to `#[Hook('node_search_ranking')]`. Only the
  `Drupal\Core\Hook\Attribute\Hook` attribute is targeted (an attribute of the
  same short name from another namespace is left untouched), and only the
  `'ranking'` argument is rewritten — the implementing method name and any
  docblocks are unchanged. `hook_ranking()` is deprecated in drupal:11.3.0 and
  removed in drupal:12.0.0; use `hook_node_search_ranking()` instead. Because
  the `node_search_ranking` hook is only invoked on Drupal minors where it
  exists, a plain rename is a silent no-op on older Drupal, and an attribute is
  not an `Expr → Expr` transformation so it cannot be BC-wrapped. The rule
  therefore lives in the opt-in `Drupal11SetList::DRUPAL_113_BREAKING` set, not
  the default deprecation set. [#1019966](https://www.drupal.org/i/1019966) /
  [change record](https://www.drupal.org/node/2690393)
- **`BlockContentSelectionExtendsRector`** — reparents entity reference
  selection plugins for the `block_content` entity type from
  `Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection` to
  `Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection`.
  The hook that automatically filtered non-reusable blocks out of those
  selections (`block_content_query_entity_reference_alter()`) is deprecated and
  removed in drupal:12.0.0; `BlockContentSelection` performs that filtering
  itself. The rewrite is gated on the `EntityReferenceSelection` attribute
  carrying `entity_types: ["block_content"]`, so `DefaultSelection` subclasses
  for other entity types are left untouched, and the canonical core
  `BlockContentSelection` is skipped so it is not made to extend itself. Ships
  in the opt-in `DRUPAL_114_BREAKING` set: `BlockContentSelection` was added to
  core alongside the deprecation (a new class, so 11.4.0) and does not exist on
  any minor below 11.4, so reparenting onto it would fatal there, and a
  `class … extends …` declaration cannot be BC-wrapped.
  [#2987159](https://www.drupal.org/i/2987159) /
  [CR](https://www.drupal.org/node/3521459).
- **`RemoveRouteBuilderDeprecatedArgsRector`** — rewrites the deprecated
  6-argument `new \Drupal\Core\Routing\RouteBuilder(...)` instantiation to the
  new 4-argument form (deprecated in drupal:11.4.0, removed in drupal:12.0.0).
  The `$module_handler` (arg 3) and `$controller_resolver` (arg 4) arguments
  were removed and `$check_provider` shifted from position 5 to position 3;
  YAML route discovery moved to the new `YamlRouteDiscovery` service. Only
  6-argument positional calls to `RouteBuilder` are matched; the new signature
  only exists on Drupal ≥ 11.4, so the change is BC-wrapped via
  `DeprecationHelper::backwardsCompatibleCall()`.
- **`RemoveDrupalToStringTraitRector`** — removes
  `use Drupal\Component\Utility\ToStringTrait;` from a class body and inserts
  an inline `public function __toString(): string { return (string)
  $this->render(); }` in its place. The trait was a PHP 7.x workaround for
  fatal errors thrown from `__toString()`; on PHP 8+ exceptions inside
  `__toString()` propagate normally, so the trait is deprecated in
  drupal:11.4.0 and removed in drupal:13.0.0. The rector preserves an
  existing `__toString()` if the class already defines one (only the
  composition is removed) and keeps any sibling traits in a comma-separated
  `use` list. The file-level `use Drupal\Component\Utility\ToStringTrait;`
  import is intentionally left in place — Rector's generic unused-import
  cleanup is a separate concern. The replacement body is pure PHP, so the
  transformed code runs on every Drupal version — no BC wrapping needed.
  [#3548957](https://www.drupal.org/i/3548957) /
  [CR](https://www.drupal.org/node/3548961).
- **`RemoveInstallSchemaSystemSequencesRector`** — removes deprecated
  `KernelTestBase::installSchema('system', 'sequences')` calls in test
  classes. The `sequences` table was deprecated in drupal:10.2.0 and
  fully removed in drupal:12.0.0 via [#3335756](https://www.drupal.org/i/3335756);
  the call now throws a `LogicException` on Drupal 12. The rule removes
  the entire statement when the second argument is the string
  `'sequences'` or an array containing only `'sequences'`; when the
  array also lists other tables, only the `'sequences'` entry is
  stripped. Receiver is type-guarded to `Drupal\KernelTests\KernelTestBase`,
  so unrelated `installSchema()` methods on other classes are left
  untouched. See change record
  [#3349345](https://www.drupal.org/node/3349345).
- **`GroupLegacyToIgnoreDeprecationsRector`** — replaces the
  `@group legacy` PHPDoc annotation with PHPUnit 10's native
  `#[\PHPUnit\Framework\Attributes\IgnoreDeprecations]` attribute on
  both method- and class-level test declarations. Drupal 11 dropped the
  `symfony/phpunit-bridge` dependency and adopted PHPUnit 10, whose
  attribute supersedes the bridge's docblock annotation. A Drupal shim
  preserves the annotation form for BC, so the rewrite is purely a
  forward-compatibility cleanup rather than a hard requirement, but
  test classes that already declare the attribute are skipped so the
  rule is idempotent. The rector strips just the `@group legacy` line
  from the docblock — surrounding annotations (`@covers`, `@dataProvider`,
  description text) are preserved — and inserts the attribute
  immediately above the method or class declaration. PHPStan cannot
  surface this deprecation because `@group legacy` is a docblock
  convention, not a code-level `@deprecated` symbol; this rule must be
  applied proactively as part of a PHPUnit 10 migration.
  [#3417066](https://www.drupal.org/i/3417066) /
  [related: #3365413](https://www.drupal.org/i/3365413).
- **`RemoveAliasManagerCacheMethodCallsRector`** — deletes calls to
  `AliasManager::setCacheKey()` and `AliasManager::writeCache()`. Both
  methods were deprecated in drupal:11.3.0 and are removed in
  drupal:13.0.0 with no replacement — they became no-ops when the path
  alias preload cache was replaced by a Fiber-based bulk-lookup strategy.
  The receiver must be typed as `\Drupal\path_alias\AliasManager` or
  `AliasManagerInterface`; this guard prevents accidentally removing
  unrelated methods that share the name (notably
  `ModuleHandler::writeCache()`). Removes the entire expression
  statement, leaving surrounding code intact. No BC wrapping is needed
  since dropping a no-op call is safe on every Drupal version.
  [#3496369](https://www.drupal.org/i/3496369) /
  [CR](https://www.drupal.org/node/3532412).
- **`EntityFormModeEmptyDescriptionToNullRector`** — rewrites
  `EntityFormMode::create([..., 'description' => '', ...])` to use `NULL`
  instead of the empty string. Setting the description property of an
  `EntityFormMode` to `''` was deprecated in drupal:11.2.0 and must be `NULL`
  in drupal:12.0.0. Matches both the short class name (`use`-imported) and
  the fully-qualified `\Drupal\Core\Entity\Entity\EntityFormMode::create()`
  form, and leaves unrelated classes (e.g. `EntityViewMode`), non-empty
  descriptions, and already-migrated NULL values untouched. The replacement
  is plain PHP, so no BC wrapping is needed.
  [#3448457](https://www.drupal.org/i/3448457) /
  [CR](https://www.drupal.org/node/3452144).
- **`ViewsBlockItemsPerPageNoneToNullRector`** — rewrites
  `ViewsBlockBase::setConfigurationValue('items_per_page', 'none')` to
  `setConfigurationValue('items_per_page', NULL)`. The string `'none'`
  was deprecated in drupal:11.2.0 and is removed in drupal:12.0.0; `NULL`
  is the canonical value for inheriting the items-per-page setting from
  the underlying view. The receiver is type-guarded to `ViewsBlockBase`
  (or any subclass), so unrelated `setConfigurationValue()` calls on
  other plugin types are left untouched. The replacement is plain PHP,
  so no BC wrapping is needed.
  [#3520946](https://www.drupal.org/i/3520946) /
  [CR](https://www.drupal.org/node/3522240).
- **`TaxonomyTermPageVariableToViewModeRector`** — replaces reads of
  `$variables['page']` with `$variables['view_mode'] === 'full'` inside
  taxonomy term preprocess hooks (procedural
  `*_preprocess_taxonomy_term()` functions and class-based
  `preprocessTaxonomyTerm()` methods). The `$variables['page']` template
  variable for taxonomy terms was deprecated in drupal:11.3.0 and is
  removed in drupal:13.0.0. Assignment targets (`$variables['page'] = …`)
  are intentionally left untouched so legacy initialisation continues to
  work, and unrelated preprocess hooks are not scanned. The replacement
  is a pure-PHP `===` comparison, so the transformed code runs on every
  Drupal version — no BC wrapping needed.
  [#3535439](https://www.drupal.org/i/3535439) /
  [CR](https://www.drupal.org/node/3542527).
- **`ReplaceNonBoolAccessRector`** — rewrites integer-literal `#access`
  values inside render arrays to proper booleans: `1` (or any non-zero
  integer) becomes `true`, and `0` becomes `false`. Passing non-boolean,
  non-`AccessResultInterface` values to `#access` was deprecated in
  drupal:11.4.0 and will be removed in drupal:13.0.0. The rule matches
  on `ArrayItem` nodes whose key is the string literal `'#access'` and
  whose value is an integer literal — it deliberately ignores booleans,
  variables, function/method calls, and any other expression, because
  the correct boolean replacement for those cannot be determined
  statically. The replacement is pure PHP (`true` / `false`), so no BC
  wrapping is needed; the transformed code runs on every Drupal version.
  [#3526250](https://www.drupal.org/i/3526250).
- **`ReplaceDialogClassOptionRector`** — rewrites the removed
  `$dialog_options['dialogClass']` key to
  `$dialog_options['classes']['ui-dialog']` in `new OpenDialogCommand(...)`,
  `new OpenModalDialogCommand(...)`, and `new OpenOffCanvasDialogCommand(...)`
  calls. `dialogClass` was deprecated in drupal:11.3.0 and removed in
  drupal:12.0.0. Receiver narrowing is by FQCN match on the resolved
  `New_->class` (`Drupal\Core\Ajax\OpenDialogCommand`,
  `Drupal\Core\Ajax\OpenModalDialogCommand`, or
  `Drupal\Core\Ajax\OpenOffCanvasDialogCommand`), and the `$dialog_options`
  argument is located per-class (4th argument for `OpenDialogCommand`, 3rd for
  the modal/off-canvas commands), so unrelated constructors with
  similarly-shaped option arrays are left alone. Handles three array
  shapes: (a) no existing `classes` key → adds `'classes' => ['ui-dialog' => $value]`;
  (b) `classes` exists without `ui-dialog` → adds `'ui-dialog' => $value` inside
  it; (c) `classes['ui-dialog']` already present and both old/new values are
  string literals → concatenates with a space. Non-literal values (variables,
  function calls, dynamic arrays) at any position cause the rule to skip
  rather than guess. The replacement form `classes['ui-dialog']` has existed
  in core since 10.3.x, so the transformed output runs on every
  drupal-rector–supported Drupal minor (D10.3+) — no BC wrapping needed.
  [#3571054](https://www.drupal.org/i/3571054) /
  [CR](https://www.drupal.org/node/3440844).
- **`RemoveToolkitArgFromImageToolkitOperationConstructorRector`** —
  removes the deprecated `ImageToolkitInterface $toolkit` 4th argument
  from `ImageToolkitOperationBase` subclass constructors and strips it
  from the matching `parent::__construct()` call. The parameter was
  deprecated in drupal:11.4.0 and will be removed in drupal:13.0.0; the
  plugin manager now injects the toolkit via `setToolkit()` after
  instantiation, enabling constructor autowiring. The rector only fires
  when the subclass directly extends `ImageToolkitOperationBase`, the
  constructor has at least five parameters, the 4th is typed exactly as
  `\Drupal\Core\ImageToolkit\ImageToolkitInterface`, and `$toolkit`
  appears exactly once in the constructor body (as the 4th argument of
  `parent::__construct()`). The usage-count walk skips nested closures
  and arrow-functions so a `$toolkit`-shadowing inner scope cannot
  inflate the count. No BC wrapping is needed: the parent signature
  accepts the union `LoggerInterface|ImageToolkitInterface`, so the
  transformed code runs on every drupal:11.4.x+ version.
  [#3559481](https://www.drupal.org/i/3559481) /
  [CR](https://www.drupal.org/node/3562304).
- **`RemoveRendererAddCacheableDependencyNonObjectRector`** — deletes calls
  to `RendererInterface::addCacheableDependency($elements, $dependency)`
  whose second argument is statically provable to be a non-object
  (`bool`, `int`, `float`, `string`, `null`, or `array`). Passing such
  values was deprecated in drupal:11.3.0 and will throw in
  drupal:13.0.0; at runtime it silently sets `max-age = 0` on the
  render array, making the page uncacheable for no useful gain. The
  rector matches at statement level so the entire expression is
  removed, never partially rewritten. The receiver is type-guarded to
  `\Drupal\Core\Render\RendererInterface` (catching the concrete
  `Renderer` and any other implementer), and the argument count must
  be exactly two — this distinguishes the call from the single-argument
  `addCacheableDependency()` defined on `BubbleableMetadata` and
  `RefinableCacheableDependencyInterface`. The PHPStan type of the
  dependency must satisfy `isObject()->no()`, so any call where the
  argument might be an object at runtime (variables typed as configs,
  entities, or `mixed`) is left untouched — the rector targets only
  the unambiguous primitive-passing mistake the deprecation was added
  to flag. No BC wrapping is needed because the removed call is a
  silent uncacheability bug on every Drupal version.
  [#3525388](https://www.drupal.org/i/3525388) /
  [CR](https://www.drupal.org/node/3525389).
- **`DrupalGetHeadersAssocArrayRector`** — converts the two deprecated
  `UiHelperTrait::drupalGet()` `$headers` argument shapes to the documented
  associative format: integer-keyed colon-separated strings
  (`['X-Requested-With: XMLHttpRequest']`) are split to
  `['X-Requested-With' => 'XMLHttpRequest']`, and `null` values
  (`['Accept-Language' => NULL]`) become empty strings
  (`['Accept-Language' => '']`). The integer-keyed path requires the
  conventional `Name: value` (colon-space) form and a name part matching
  `[A-Za-z][A-Za-z0-9-]*`, so incidental colon-containing strings (URLs,
  paths) are not silently split. Guarded against `Drupal\Tests\BrowserTestBase`
  so `KernelTestBase` (which uses `HttpKernelUiHelperTrait` and does not
  emit this deprecation) is left alone. Deprecated in drupal:11.1.0,
  removed in drupal:12.0.0; replacement is plain PHP so no BC wrapping
  is needed. Live-tested against `pager_serializer`.
  [#3440169](https://www.drupal.org/i/3440169) /
  [CR (indexed headers)](https://www.drupal.org/node/3456178) /
  [CR (null values)](https://www.drupal.org/node/3456233).
- **`ReplaceHideShowWithPrintedRector`** — replaces statement-level calls to the
  deprecated global `hide()` and `show()` functions (deprecated in drupal:11.4.0,
  removed in drupal:13.0.0) with direct `$element['#printed'] = TRUE/FALSE`
  assignment. Expression-context uses (where the return value is captured) are
  intentionally skipped because the original returns the element while the
  rewrite would not. Live-tested against `fpa`, `saml_sp`, `vertical_tabs_config`,
  and `field_group_background_image`.
  [#2258355](https://www.drupal.org/i/2258355) /
  [CR](https://www.drupal.org/node/3261271).
- **`GetDrupalRootToRootPropertyRector`** — rewrites calls to
  `DrupalKernelInterface::getDrupalRoot()` to direct access of the
  `$this->root` property on Drupal base test classes (BrowserTestBase,
  KernelTestBase, UnitTestCase). `getDrupalRoot()` was deprecated in
  drupal:11.4.0 and removed in drupal:13.0.0. The receiver is
  type-guarded to the listed base classes (and their subclasses), so
  unrelated `getDrupalRoot()` methods on other classes are left alone.
  No BC wrapping is needed — the `$root` property has existed on the
  trait since Drupal 10.x.
  [#3589047](https://www.drupal.org/i/3589047) /
  [CR](https://www.drupal.org/node/3574112).
- **`ReplaceLocaleTranslationPathConfigRector`** — rewrites chained
  `\Drupal::config('locale.settings')->get('translation.path')` (and
  equivalents via `configFactory()->get('locale.settings')->get(...)`,
  `$this->config('locale.settings')->get(...)`, and similar) to
  `\Drupal\Core\Site\Settings::get('locale_translation_path', 'public://translations')`.
  The `locale.settings:translation.path` config key was deprecated in
  drupal:11.4.0 and is removed in drupal:13.0.0; the interface
  translations directory path must now be set as
  `$settings['locale_translation_path']` in `settings.php`. On older
  Drupal the value still lives in config, so the replacement is
  BC-wrapped with `DeprecationHelper::backwardsCompatibleCall()`.
  Matching is purely structural — two literal keys
  (`'locale.settings'` and `'translation.path'`) must both appear in the
  expected positions, so unrelated config reads and standalone
  `$config->get('translation.path')` calls are left untouched.
  **Caveat:** the BC wrapper gates on `\Drupal::VERSION`, not on where
  the value is stored. Before running this rule, confirm that any
  customised translation path has been moved to
  `$settings['locale_translation_path']` in `settings.php`; otherwise
  the new branch silently returns the default
  `'public://translations'` even when the config still holds the
  customised value. PHPStan / upgrade_status cannot detect this
  deprecation — the deprecated symbol is the config key, not a PHP API
  with `@deprecated` or `trigger_error()`, so this rule must be applied
  proactively as part of an 11.4 → 13 migration plan.
  [#3571593](https://www.drupal.org/i/3571593) /
  [CR](https://www.drupal.org/node/3571594).
- **`ViewsConfigUpdaterClassResolverToServiceRector`** — rewrites
  `\Drupal::classResolver(\Drupal\views\ViewsConfigUpdater::class)` to
  `\Drupal::service(\Drupal\views\ViewsConfigUpdater::class)`. In
  drupal:11.3.0 `ViewsConfigUpdater` was registered as a service;
  `classResolver()` returns a fresh instance on each call, so state set via
  `setDeprecationsEnabled(FALSE)` was lost across hook invocations. The new
  call only resolves on Drupal ≥ 11.3.0 (the service isn't registered on
  older versions), so the replacement is BC-wrapped with
  `DeprecationHelper::backwardsCompatibleCall()`. Three layered guards
  ensure only the targeted call shape is touched: receiver must be
  `\Drupal`, method must be `classResolver`, and the single argument must be
  `\Drupal\views\ViewsConfigUpdater::class`.
  [#3529274](https://www.drupal.org/i/3529274) /
  [CR](https://www.drupal.org/node/3530638).
- **`ReplaceExpectDeprecationRector`** — migrates removed test framework methods
  to their PHPUnit 11+ replacements. Renames are BC-wrapped with
  `DeprecationHelper::backwardsCompatibleCall()` so tests keep passing on both
  pre-11.4 (old methods) and 11.4+ (new methods). Covers:
  `$this->expectDeprecation($msg)` and `$this->expectDeprecationMessage($msg)` →
  `$this->expectUserDeprecationMessage($msg)`;
  `$this->expectDeprecationMessageMatches($p)` →
  `$this->expectUserDeprecationMessageMatches($p)`; bare
  `$this->expectDeprecation()` (no-arg PHPUnit form) → removed.
  `ExpectDeprecationTrait` is deprecated in drupal:11.4.0 and removed in
  drupal:12.0.0.
  [#3550268](https://www.drupal.org/i/3550268) /
  [CR](https://www.drupal.org/node/3545276).
- **`ReplaceCommentPreviewConstantsRector`** — rewrites the legacy
  `DRUPAL_DISABLED` / `DRUPAL_OPTIONAL` / `DRUPAL_REQUIRED` constant arguments
  to `CommentTestBase::setCommentPreview()` to the corresponding
  `Drupal\comment\CommentPreviewMode` enum case. Only these named constants are
  matched (`ConstFetch` nodes) — a bare integer literal such as
  `setCommentPreview(0)` is left untouched. Passing the constants was
  deprecated in drupal:11.3.0 and is removed in drupal:13.0.0. BC-wrapped
  via `DeprecationHelper::backwardsCompatibleCall()` so the rewrite still
  runs on pre-11.3 Drupal where the enum doesn't yet exist.
  [#3538660](https://www.drupal.org/i/3538660) /
  [CR](https://www.drupal.org/node/3538678).
- **`RemovePhpUnitCompatibilityTraitRector`** — removes
  `use Drupal\Tests\PhpUnitCompatibilityTrait;` from test class
  declarations. The trait was a forward-compatibility shim for PHPUnit
  API differences across versions; it is **deleted from Drupal core in
  Drupal 12** via [#3582118](https://www.drupal.org/i/3582118), at which
  point any test class still composing the trait fatal-errors at
  autoload time because the trait class no longer exists.

  **Gated to Drupal 12 only — and deliberately off by default.** The
  trait still exists on Drupal 10 (and may still hold shim methods that
  tests depend on) and is an empty no-op on Drupal 11. Because the
  trait composition is a structural `Class_` change, not an Expr → Expr
  rewrite, it cannot be BC-wrapped with `DeprecationHelper`. Running
  the rule prematurely on a D10-only codebase risks silently stripping
  a composition that the tests still rely on. It is registered in the
  default `drupal-11.4-deprecations.php` set but gated with
  `DrupalIntroducedVersionConfiguration('12.0.0')`, so it never fires unless
  the consumer explicitly sets the target Drupal version to `12.0.0` or higher
  via `DrupalRectorSettings::setDrupalVersion('12.0.0')`. The orphan
  top-of-file `use Drupal\Tests\PhpUnitCompatibilityTrait;` import is
  left in place — PHP never resolves an unused alias, so it remains
  harmless on D12; cleanup is optional and out of scope.
  [#3582118](https://www.drupal.org/i/3582118).
- **New opt-in "breaking" sets**, one per Drupal 11 minor:
  `Drupal11SetList::DRUPAL_111_BREAKING`, `DRUPAL_112_BREAKING`,
  `DRUPAL_113_BREAKING`, `DRUPAL_114_BREAKING`. Each is loaded from
  `config/drupal-11/drupal-11.X-breaking.php`. Rules in these sets rewrite
  code into a form that does NOT run on every drupal-rector-supported minor —
  typically because the replacement symbol was introduced *together with* the
  deprecation and does not exist on older minors, and the rewrite (class
  rename, trait composition, etc.) is structural and cannot be BC-wrapped.
  None of the breaking sets is included in `DRUPAL_11X` or `DRUPAL_11`;
  consumers must load each one explicitly after committing to drop the older
  minor(s) named in that file's docblock.
- **Reclassified existing `RenameClassRector` entries as breaking**, moved
  out of the default `drupal-11.X-deprecations.php` files and into the new
  per-minor breaking sets. Targets verified missing on Drupal 10.6.x:
  - `DRUPAL_111_BREAKING`: `path_alias\AliasWhitelist[Interface]` →
    `path_alias\AliasPrefixList[Interface]`
    ([#3151086](https://www.drupal.org/i/3151086) /
    [CR](https://www.drupal.org/node/3467559)).
  - `DRUPAL_112_BREAKING`: `Core\Entity\Query\Sql\pgsql\{QueryFactory,Condition}`
    → `pgsql\EntityQuery\*`
    ([#3488572](https://www.drupal.org/i/3488572) /
    [CR](https://www.drupal.org/node/3488580));
    `migrate_drupal\Plugin\migrate\source\{ContentEntity,ContentEntityDeriver}`
    → `migrate\Plugin\migrate\source\*`
    ([#3498915](https://www.drupal.org/i/3498915) /
    [CR](https://www.drupal.org/node/3498916)).
  - `DRUPAL_113_BREAKING`: `workspaces\WorkspaceAssociation[Interface]` →
    `workspaces\WorkspaceTracker[Interface]`
    ([#3551446](https://www.drupal.org/i/3551446) /
    [CR](https://www.drupal.org/node/3551450)); the four
    `block_content\Access\*` aliases (`AccessGroupAnd`,
    `DependentAccessInterface`, `RefinableDependentAccessInterface`,
    `RefinableDependentAccessTrait`) → their canonical `Core\Access\*` homes.
    The canonical classes were introduced in drupal:11.3.0 (not earlier) and
    do not exist on any Drupal 10 branch, and `RenameClassRector` rewrites
    structural `use`/`extends`/`implements`/`::class` nodes that cannot be
    BC-wrapped, so the rewrite fatals on Drupal 10
    ([#3571874](https://www.drupal.org/i/3571874) /
    [CR](https://www.drupal.org/node/3527501)).
  - `DRUPAL_114_BREAKING`: `menu_link_content\Plugin\migrate\process\{LinkOptions,LinkUri}`
    → `migrate\Plugin\migrate\process\*`
    ([#3560075](https://www.drupal.org/i/3560075) /
    [CR](https://www.drupal.org/node/3572239)).
- **Dropped**: the planned `Drupal\jsonapi\EventSubscriber\ResourceResponseValidator`
  → `Drupal\jsonapi_response_validator\…` rename (#3472008) is **not shipped**
  in any set. The replacement lives in `core/modules/jsonapi/tests/modules/`,
  i.e. a core test module that production code cannot rely on being loaded;
  rewriting the production FQCN to that test-module FQCN would fatal on D10
  AND on any production D11 site that does not enable that test module.
- Class-rename entry for `LibraryDiscovery`:
  `Drupal\Core\Asset\LibraryDiscovery` → `Drupal\Core\Asset\LibraryDiscoveryInterface`.
  The concrete class was deprecated in drupal:11.1.0 and removed in drupal:12.0.0;
  the `library.discovery` service is now backed by `LibraryDiscoveryCollector`,
  so consumer code should type-hint the interface. Registered via Rector's
  built-in `RenameClassRector` in `drupal-11.1-deprecations.php`.
  `LibraryDiscoveryInterface` has existed since Drupal 10.0.x, so the rewrite is
  safe across all supported Drupal 10 and 11 minors.
  [#3462871](https://www.drupal.org/i/3462871) (deprecation) /
  [#3571057](https://www.drupal.org/i/3571057) (removal) /
  [CR](https://www.drupal.org/node/3462970).
- Class-rename entry for `EntityPermissionsRouteProviderWithCheck`:
  `Drupal\user\Entity\EntityPermissionsRouteProviderWithCheck` →
  `Drupal\user\Entity\EntityPermissionsRouteProvider`. The `WithCheck` variant was
  deprecated in drupal:11.1.0 and removed in drupal:12.0.0; the base provider has
  existed since Drupal 10.0.x, so the rewrite is safe across all supported Drupal
  10 and 11 minors. Registered via Rector's built-in `RenameClassRector` in
  `drupal-11.1-deprecations.php`. **Access-check semantics:** the `WithCheck`
  variant layered a `_custom_access` requirement (`EntityPermissionsForm::access`,
  also removed) on top of the base route to deny access when an entity type had
  no entity-specific permissions; the base provider already enforces
  `_permission: administer permissions`, so the security boundary is preserved
  and only the "no permissions defined → deny" convenience check is dropped.
  Subclass overrides that re-added the custom check are NOT rewritten —
  `RenameClassRector` only updates the parent class reference, so owners of such
  subclasses must port any remaining access logic into the route definition
  (the new model adds permission requirements directly on the route).
  **Limitation — doctrine annotation strings are not rewritten.** Real-world
  contrib usage is almost exclusively the entity-annotation form
  (`"permissions" = "Drupal\user\Entity\EntityPermissionsRouteProviderWithCheck"`
  inside a `@ContentEntityType` / `@ConfigEntityType` docblock).
  `RenameClassRector` only touches PHP `Name` nodes (`use`, `extends`,
  `implements`, `::class`, typehints, `instanceof`) — it does **not** scan
  string literals inside doctrine annotations. Audited against Drupal contrib
  (api.tresbien.tech, 2026-05-27): three modules reference the class — all via
  the annotation form — and zero contrib modules use it in PHP code. Owners of
  those modules must hand-edit the annotation string; this rector is a safety
  net for `use` / `extends` patterns and for entity types that switch to
  PHP-attribute syntax.
  [#3573870](https://www.drupal.org/i/3573870) /
  [CR](https://www.drupal.org/node/3384745).

### Changed

- **Migration note for existing consumers of `Drupal11SetList::DRUPAL_111` /
  `DRUPAL_112` / `DRUPAL_113`**: the class-rename entries listed above have
  been moved out of the per-minor `DRUPAL_11X` aggregates into new opt-in
  `*_BREAKING` sets. If you currently rely on rector rewriting any of these
  symbols, add the matching `*_BREAKING` set to your rector config alongside
  your existing `DRUPAL_11X` include — otherwise the rewrites silently stop:
  - `DRUPAL_111`: `AliasWhitelist[Interface]` → `AliasPrefixList[Interface]`
    now requires `DRUPAL_111_BREAKING`.
  - `DRUPAL_112`: `Core\Entity\Query\Sql\pgsql\{QueryFactory,Condition}` →
    `pgsql\EntityQuery\*` and the `migrate_drupal` → `migrate` source-plugin
    moves now require `DRUPAL_112_BREAKING`.
  - `DRUPAL_113`: `WorkspaceAssociation[Interface]` →
    `WorkspaceTracker[Interface]` now requires `DRUPAL_113_BREAKING`.

  The breaking sets are not transitively included by `DRUPAL_11X` or
  `DRUPAL_11`; consumers must load each one explicitly after committing to
  drop the older minor(s) named in that file's docblock.
- Guide: [Running against a Drupal 10 project](docs/running-against-drupal-10.md) — covers the
  direct install and a standalone-runner recipe for sites whose PHPStan 1 tooling conflicts with
  Rector 2's PHPStan 2 requirement.
- **`HookConvertRector`** now produces lint-clean hook classes:
  - Methods whose body never references `$this` are declared `static`
    (satisfies the `canvas.requireStaticMethods` / "method does not use `$this`
    and should be declared static" check). The body originates from a
    procedural function, so this is safe by construction.
  - Global `t()` calls are rewritten to `$this->t()` and the generated class
    gains `use Drupal\Core\StringTranslation\StringTranslationTrait;` (clears
    the `DrupalPractice.Objects.GlobalFunction.GlobalFunction` warning). Because
    `$this->t()` introduces `$this`, those methods correctly remain
    non-static — the two rules compose rather than conflict.

### Fixed

- **`ReplaceEntityOriginalPropertyRector`** now handles `isset()` and `unset()`
  correctly instead of producing a parse-time fatal. Those constructs accept
  only a variable, so blindly rewriting `$entity->original` to the
  `$entity->getOriginal()` method call (e.g. `isset($entity->getOriginal())`)
  was invalid PHP. Mirroring `EntityBase`'s magic methods:
  - `isset($entity->original)` → `$entity->getOriginal() !== NULL`
    (`__isset()` returns `getOriginal()`), BC-wrapped in `DeprecationHelper`.
  - `unset($entity->original)` → `$entity->setOriginal(NULL)` (`__unset()` calls
    `setOriginal(NULL)`), BC-wrapped with `$entity->original = NULL` as the
    pre-11.2 path. In a multi-operand `unset()`, only the `->original` operand
    is rewritten; the rest stay in a residual `unset()`.

  Only the *direct/outermost* operand is fatal as a method call, so nested
  fetches are rewritten normally: `isset($entity->original->field)` →
  `isset($entity->getOriginal()->field)` and likewise for `unset()` (both parse
  fine — only a bare method call as the outermost operand is fatal). A fetch
  used as an array key, e.g. `isset($map[$entity->original])`, and `empty()`
  (which accepts arbitrary expressions) are also rewritten. The only form left
  untouched is the direct operand of a multi-operand `isset()` —
  `isset($entity->original, $other)` — where rewriting `->original` would
  produce the fatal `isset($entity->getOriginal(), $other)`.
- Loading the Drupal 9 and Drupal 11 sets together no longer crashes at
  container-build time. The Drupal 9 `FunctionToFirstArgMethodRector` (and the
  Drupal 8 `DrupalServiceRenameRector`) subclass the generic rule, so Rector
  delivered the generic rule's configuration to the subclass instance as well
  (`afterResolving` callbacks match by `instanceof`); the subclass' strict type
  guard then threw. The subclasses now ignore configuration that is not their own.
  This unblocks running the full, bundled rule set — including the D10-era
  deprecations that live in the Drupal 11 set — against Drupal 10 sites.

[1.0.0-beta1]: https://github.com/palantirnet/drupal-rector/releases/tag/1.0.0-beta1
## [1.0.0-alpha1] — 2026-06-01

### Changed

This will be the first alpha of the 1.0 line. Adds full Drupal 11 deprecation coverage 
(versions 11.0 through 11.4), a new container-managed settings service that gives users 
explicit control over backward-compatibility wrapping, a documented set of Claude Code 
skills for building further rectors, and drops support for Rector 1.

Real-world validated end-to-end:

- Applied to [Drupal Canvas](https://www.drupal.org/project/canvas) on the
  `canvas-3589155` branch — the full Canvas test suite passed
  ([pipeline 825601](https://git.drupalcode.org/issue/canvas-3589155/-/pipelines/825601)).
- Run across **the whole of Drupal contrib** via
  [project_analysis](https://git.drupalcode.org/project/project_analysis) — code
  branch [`master-next`](https://git.drupalcode.org/project/project_analysis/-/tree/master-next?ref_type=heads),
  generated patches under
  [`results-next`](https://git.drupalcode.org/project/project_analysis/-/tree/results-next?ref_type=heads).
  Spot-checking those patches surfaced and drove the late-PR fixes (PDO receiver
  guard, BC-wrapper mutation, `ConstantToClassConstantRector` generalisation,
  extra `MethodToMethodWithCheckRector` patterns).

### Highlights

- **Drupal 11.0–11.4 coverage.** 72 rectors wired across five per-minor-version
  deprecation set lists, covering function → service / function → static / function
  → method-on-first-arg / class-constant → enum / class rename / argument removal
  / property → method / and configuration-key rewrites.
- **Drupal 10.3 additions.** 4 new D10 rectors covering `REQUEST_TIME`,
  `ThemeHandlerInterface::rebuildThemeData()`, `ModuleHandlerInterface::getName()`,
  and `FileSystemInterface::EXISTS_*` → `FileExists` enum.
- **`DrupalRectorSettings` service.** Three knobs for tuning runtime behaviour:
  toggle BC wrapping on/off, set a `minimumCoreVersionSupported` (contrib modules),
  and override the detected Drupal version (tests). Registered via
  `$rectorConfig->singleton(DrupalRectorSettings::class, …)` in the shipped
  `rector.php`.
- **Generic data-driven rectors.** A new shared rector
  (`FunctionCallRemovalRector`) and two extracted from per-version folders
  (`DrupalServiceRenameRector`, `FunctionToFirstArgMethodRector`) now live in
  `src/Rector/Deprecation/` alongside the established shared rectors, so each
  Drupal version can register them via config-only entries rather than
  per-version subclasses. See the **Generic shared rectors** subsection below
  for a labelled list of what's new vs. refactored vs. unchanged.
- **BC wrapping covers all expression rewrites.** Previously
  `AbstractDrupalCoreRector::createBcCallOnExpr()` only wrapped CallLike → CallLike
  rewrites; it now wraps any `Node\Expr` → `Node\Expr` transformation
  (`ClassConstFetch`, `PropertyFetch`, etc.), so mixed-type rewrites get
  `DeprecationHelper::backwardsCompatibleCall()` automatically.
- **Claude Code skills.** Four `.claude/skills/` entries — `/rector-discover`,
  `/rector-implement`, `/rector-qa`, `/rector-live-test` — drive the conversion
  pipeline from [dbuytaert/drupal-digests](https://github.com/dbuytaert/drupal-digests)
  rules into shipped rector classes with tests. Includes the 700-line canonical
  conversion prompt and three reusable recipes.

### Added

#### Infrastructure
- **`rector-hook-convert.php`** — dedicated configuration that registers
  only `HookConvertRector`, for running hook conversion as a separate second
  pass. `HookConvertRector` writes the generated `src/Hook/*Hooks.php` to disk
  outside Rector's file pipeline, so bundling it with the deprecation sets would
  copy un-fixed hook bodies into the new class. Documented in the README
  ("Converting hooks to OOP hook classes"): run deprecations first, then this
  config.
- **`DrupalRectorSettings`** (`src/Services/DrupalRectorSettings.php`) — container-
  managed settings object with `enableBackwardCompatibility()` /
  `disableBackwardCompatibility()` / `setMinimumCoreVersionSupported(string)` /
  `setDrupalVersion(?string)` methods. Resolved via
  `static::getContainer()->make(DrupalRectorSettings::class)` in tests.
- **`AbstractDrupalRectorTestCase`** (`tests/src/AbstractDrupalRectorTestCase.php`)
  — replaces the prior namespaced-class `\Drupal` hack with a proper
  `setUp`/`tearDown` pattern that resets the shared `DrupalRectorSettings`
  singleton between tests, preventing version-override leaks.
- **`createBcCallOnExpr()` widened to `Node\Expr`** in
  `src/Rector/AbstractDrupalCoreRector.php`, so non-CallLike rewrites are
  BC-wrapped too.
- **`.gitattributes`** added with `export-ignore` entries for `.claude/`,
  `.github/`, `tests/`, `fixtures/`, `docs/`, `scripts/`, `phpstan*.neon`,
  `phpunit.xml`, `rector.php`, etc. — keeps the Composer dist archive small and
  clean. (Verify with `git archive HEAD | tar t | grep -c .claude` — expect 0.)
- **25 PHPStan stubs** under `stubs/` covering core classes the rectors type-guard
  against (`ModuleHandlerInterface`, `EntityInterface`, `ConfigEntityInterface`,
  `StatementPrefetchIterator`, `CachePluginBase`, `ThemeHandlerInterface`,
  `SessionManagerInterface`, …).

#### Drupal 11 deprecation rules

Every rule wired across `config/drupal-11/drupal-11.{0,1,2,3,4}-deprecations.php`
is listed below. Each row links to the Drupal.org change record (or, if no
dedicated change record exists, the parent issue). Configurable generic rectors
(`FunctionToServiceRector`, `FunctionToStaticRector`, `FunctionCallRemovalRector`,
`ConstantToClassConstantRector`, `ClassConstantToClassConstantRector`,
`MethodToMethodWithCheckRector`, `FunctionToFirstArgMethodRector`,
`RenameClassRector`) may appear in multiple rows when they handle several
distinct deprecations in the same minor.

##### Drupal 11.0

| Rule | What it does | Change record |
|---|---|---|
| `GetNameToNameRector` | `TestCase::getName()` → `name()` | [node/3217904](https://www.drupal.org/node/3217904) |
| `RemoveStateCacheSettingRector` | Remove `$settings['state_cache']` — state caching is permanently enabled | [node/2575105](https://www.drupal.org/node/2575105) |
| `ReplaceRequestTimeConstantRector` | `REQUEST_TIME` → `\Drupal::time()->getRequestTime()` | [node/3395986](https://www.drupal.org/node/3395986) |
| `StripMigrationDependenciesExpandArgRector` | `getMigrationDependencies($expand)` → drop the boolean arg | [node/3442785](https://www.drupal.org/node/3442785) |
| `MigrateSqlGetMigrationPluginManagerRector` | `Sql::getMigrationPluginManager()` → `$this->migrationPluginManager` property | [node/3282894](https://www.drupal.org/node/3282894) |

##### Drupal 11.1

| Rule | What it does | Change record |
|---|---|---|
| `PluginBaseIsConfigurableRector` | `PluginBase::isConfigurable()` → `instanceof ConfigurableInterface` check | [node/2946122](https://www.drupal.org/node/2946122) |
| `RenameClassRector` | `AliasWhitelist[Interface]` → `AliasPrefixList[Interface]`; `MatchingRouteNotFoundException` → `ResourceNotFoundException` | [node/3467559](https://www.drupal.org/node/3467559) |
| `MethodToMethodWithCheckRector` | `AliasManager::pathAliasWhitelistRebuild()` → `pathAliasPrefixListRebuild()` | [node/3467559](https://www.drupal.org/node/3467559) |
| `RemoveModuleHandlerDeprecatedMethodsRector` | `ModuleHandler::writeCache()` removed; `getHookInfo()` returns `[]` | [node/3368812](https://www.drupal.org/node/3368812) |
| `ReplaceLocaleConfigBatchFunctionsRector` | Rename `locale_config_batch_set_config_langcodes()` and `locale_config_batch_refresh_name()` | [node/3575254](https://www.drupal.org/node/3575254) |
| `RemoveUpdaterPostInstallMethodsRector` | Remove `Updater::postInstall*()` methods (no replacement) | [node/3461934](https://www.drupal.org/node/3461934) |
| `BlockContentTestBaseStringToArrayRector` | `BlockContentTestBase::createBlockContentType($string)` → array arg | [node/3473739](https://www.drupal.org/node/3473739) |
| `MovePointerToMouseOverRector` | `movePointerTo($css)` → `getSession()->getDriver()->mouseOver($xpath)` | [node/3460567](https://www.drupal.org/node/3460567) |
| `ReplaceAddCachedDiscoveryMethodCallRector` | `addMethodCall('addCachedDiscovery', …)` on `plugin.cache_clearer` removed | [node/3442229](https://www.drupal.org/node/3442229) |
| `FunctionToStaticRector` | `drupal_common_theme()` → `ThemeCommonElements::commonElements()` | [node/3488176](https://www.drupal.org/node/3488176) |

##### Drupal 11.2

| Rule | What it does | Change record |
|---|---|---|
| `StatementPrefetchIteratorFetchColumnRector` | `StatementPrefetchIterator::fetchColumn()` → `fetchField()` | [node/3490312](https://www.drupal.org/node/3490312) |
| `MethodToMethodWithCheckRector` | `CacheBackendInterface::invalidateAll()` → `deleteAll()` | [node/3500622](https://www.drupal.org/node/3500622) |
| `FunctionToServiceRector` | `template_preprocess_{html,page,container,links,time,datetime_form,datetime_wrapper}()` → `ThemePreprocess` / `DatePreprocess` service methods | [node/3504125](https://www.drupal.org/node/3504125) |
| `FunctionCallRemovalRector` | Remove `template_preprocess()`, `update_clear_update_disk_cache()`, `update_delete_file_if_stale()`, `_update_manager_{cache_directory,extract_directory,unique_identifier}()` | [node/3501136](https://www.drupal.org/node/3501136) |
| `RemoveModuleHandlerAddModuleCallsRector` | Remove `ModuleHandler::addModule()` / `addProfile()` (no-ops) | [node/3550193](https://www.drupal.org/node/3550193) |
| `RemoveHandlerBaseDefineExtraOptionsRector` | Remove `HandlerBase::defineExtraOptions()` overrides (dead code) | [node/3486781](https://www.drupal.org/node/3486781) |
| `FunctionToStaticRector` | `drupal_requirements_severity()` → `RequirementSeverity::maxSeverityFromRequirements()` | [node/3497049](https://www.drupal.org/node/3497049) |
| `FunctionToServiceRector` | `views_field_default_views_data()` and `_views_field_get_entity_type_storage()` → services | [node/3489415](https://www.drupal.org/node/3489415) |
| `ConstantToClassConstantRector` | Global `REQUIREMENT_INFO/OK/WARNING/ERROR` → `RequirementSeverity::*` | [node/3575841](https://www.drupal.org/node/3575841) |
| `RemoveTwigNodeTransTagArgumentRector` | Drop the 6th `$tag` argument from `TwigNodeTrans` constructor calls | [node/3474692](https://www.drupal.org/node/3474692) |
| `ReplaceAlphadecimalToIntNullRector` | `Number::alphadecimalToInt(null\|'')` → `0` | [node/3494472](https://www.drupal.org/node/3494472) |
| `ReplaceFieldgroupToFieldsetRector` | Render-array `'#type' => 'fieldgroup'` → `'fieldset'` | [node/3515272](https://www.drupal.org/node/3515272) |
| `ReplacePdoFetchConstantsRector` | `\PDO::FETCH_*` → `\Drupal\Core\Database\Statement\FetchAs::*` (StatementInterface receiver only) | [node/3488338](https://www.drupal.org/node/3488338) |
| `ReplaceDateTimeRangeConstantsRector` | `DateTimeRangeConstantsInterface::{BOTH,START_DATE,END_DATE}` → enum `->value`; `datetime_type_field_views_data_helper()` → service | [node/3574901](https://www.drupal.org/node/3574901) |
| `FunctionToFirstArgMethodRector` | `file_get_content_headers($file)` → `$file->getDownloadHeaders()` | [node/3494172](https://www.drupal.org/node/3494172) |
| `ReplaceSessionWritesWithRequestSessionRector` | `$_SESSION['key'] = $value` → `\Drupal::request()->getSession()->set(...)` | [node/3518914](https://www.drupal.org/node/3518914) |
| `ReplaceEditorLoadRector` | `editor_load($format_id)` → `entityTypeManager()->getStorage('editor')->load($format_id)` | [node/3509245](https://www.drupal.org/node/3509245) |
| `ReplaceEntityOriginalPropertyRector` | `$entity->original` magic property → `$entity->getOriginal()` / `setOriginal()`; nullsafe-aware | [node/3571065](https://www.drupal.org/node/3571065) |
| `RenameStopProceduralHookScanRector` | `#[StopProceduralHookScan]` → `#[ProceduralHookScanStop]` | [node/3495943](https://www.drupal.org/node/3495943) |
| `RenameClassRector` | `Drupal\Core\Entity\Query\Sql\pgsql\*` → `Drupal\pgsql\Entity\Query\*`; `jsonapi` ResourceResponseValidator move | [node/3488572](https://www.drupal.org/node/3488572) |
| `RemoveCacheTagChecksumAssertionsRector` | Remove `CacheTagChecksumCount` / `CacheTagIsValidCount` performance-trait assertions | [node/3511149](https://www.drupal.org/node/3511149) |
| `RemoveRootFromCreateConnectionOptionsFromUrlRector` | `Connection::createConnectionOptionsFromUrl()` — drop `$root` parameter | [node/3511287](https://www.drupal.org/node/3511287) |
| `ClassConstantToClassConstantRector` | `SystemManager::REQUIREMENT_*` → `RequirementSeverity::*` | [node/3410939](https://www.drupal.org/node/3410939) |

##### Drupal 11.3

| Rule | What it does | Change record |
|---|---|---|
| `ReplaceCommentManagerGetCountNewCommentsRector` | `CommentManager::getCountNewComments()` → `HistoryManager::getCountNewComments()` | [node/3551729](https://www.drupal.org/node/3551729) |
| `LoadAllIncludesRector` | `ModuleHandler::loadAllIncludes()` → explicit `foreach (getModuleList() …) loadInclude()` | [node/3536432](https://www.drupal.org/node/3536432) |
| `NodeStorageDeprecatedMethodsRector` | `NodeStorage::revisionIds()` / `userRevisionIds()` → entity query chains; `countDefaultLanguageRevisions()` removed | [node/3519187](https://www.drupal.org/node/3519187) |
| `FunctionToServiceRector` | `node_mass_update()` → `NodeBulkUpdate::process()` | [node/3533083](https://www.drupal.org/node/3533083) |
| `FunctionToServiceRector` | `template_preprocess_layout()` → `LayoutDiscoveryThemeHooks::preprocessLayout()` | [node/3504125](https://www.drupal.org/node/3504125) |
| `ReplaceTwigExtensionRector` | `twig_extension()` → `'.html.twig'` string literal | [node/1685492](https://www.drupal.org/node/1685492) |
| `ReplaceNodeModuleProceduralFunctionsRector` | `node_type_get_names()` → `entity_type.bundle.info` service; `node_get_type_label($node)` → `$node->getBundleEntity()->label()` | [node/3516778](https://www.drupal.org/node/3516778) |
| `FunctionCallRemovalRector` | Remove `block_content_add_body_field()` calls (replaced by config) | [node/3535528](https://www.drupal.org/node/3535528) |
| `FunctionToFirstArgMethodRector` | `comment_uri($comment)` → `$comment->permalink()` | [node/2010202](https://www.drupal.org/node/2010202) |
| `ReplaceNodeAccessViewAllNodesRector` | `node_access_view_all_nodes()` → entity-type-manager access-control chain | [node/3038909](https://www.drupal.org/node/3038909) |
| `FunctionToServiceRector` | `responsive_image_*()` helpers → `responsive_image.builder` service | [node/3548329](https://www.drupal.org/node/3548329) |
| `ReplaceNodeAddBodyFieldRector` | `node_add_body_field()` → `$this->createBodyField()` (BodyFieldCreationTrait) | [node/3516778](https://www.drupal.org/node/3516778) |
| `ReplaceUserSessionNamePropertyRector` | `$userSession->name` read → `getAccountName()` | [node/3513877](https://www.drupal.org/node/3513877) |
| `FunctionToStaticRector` | `file_system_settings_submit()` → `FileSystemSettingsForm::submit()` | [node/3534091](https://www.drupal.org/node/3534091) |
| `FileManagedFileSubmitRector` | `'file_managed_file_submit'` string callback → `[ManagedFile::class, 'submit']` array callable | [node/3534091](https://www.drupal.org/node/3534091) |
| `ConstantToClassConstantRector` | Global `JSONAPI_FILTER_AMONG_*` → `\Drupal\jsonapi\JsonApiFilter::AMONG_*` | [node/3495601](https://www.drupal.org/node/3495601) |
| `ReplaceNodeSetPreviewModeRector` | `DRUPAL_DISABLED/OPTIONAL/REQUIRED` (and integers) in `setPreviewMode()` → `NodePreviewMode` enum | [node/3538666](https://www.drupal.org/node/3538666) |
| `FileSystemBasenameToNativeRector` | `FileSystem::basename()` → PHP native `basename()` | [node/3530869](https://www.drupal.org/node/3530869) |
| `ErrorCurrentErrorHandlerRector` | `Error::currentErrorHandler()` → PHP `get_error_handler()` | [node/3529500](https://www.drupal.org/node/3529500) |
| `ReplaceThemeGetSettingRector` | `theme_get_setting()` → `ThemeSettingsProvider` service; `_system_default_theme_features()` → `ThemeSettingsProvider::DEFAULT_THEME_FEATURES` | [node/3573896](https://www.drupal.org/node/3573896) |
| `RemoveRootFromConvertDbUrlRector` | `Database::convertDbUrlToConnectionInfo($url, $root, …)` — drop the `$root` arg | [node/3511287](https://www.drupal.org/node/3511287) |
| `RenameClassRector` | `workspaces.association` service / `WorkspaceAssociation*` → `workspaces.tracker` / `WorkspaceTracker*` | [node/3551450](https://www.drupal.org/node/3551450) |

##### Drupal 11.4

| Rule | What it does | Change record |
|---|---|---|
| `ViewsPluginHandlerManagerRector` | `Views::pluginManager()` / `handlerManager()` → `plugin.manager.views.*` services | [node/3566982](https://www.drupal.org/node/3566982) |
| `FunctionToServiceRector` | `node_access_grants()` → `NodeGrantsHelper::nodeAccessGrants()` | [node/3578055](https://www.drupal.org/node/3578055) |
| `NodeAccessRebuildFunctionsRector` | `node_access_rebuild()` / `node_access_needs_rebuild()` → `NodeAccessRebuild` service | [node/3534610](https://www.drupal.org/node/3534610) |
| `FilterFormatFunctionsToServiceRector` | `filter_formats()`, `filter_get_roles_by_format()`, `filter_get_formats_by_role()`, `filter_default_format()`, `filter_fallback_format()` → `FilterFormatRepositoryInterface` service | [node/3035368](https://www.drupal.org/node/3035368) |
| `MediaFilterFormatEditFormValidateRector` | `media_filter_format_edit_form_validate()` → `MediaHooks::formatEditFormValidate()` | [node/3566774](https://www.drupal.org/node/3566774) |
| `DeprecatedFilterFunctionsRector` | `_filter_autop()` / `_filter_html_escape()` / `_filter_html_image_secure_process()` → `plugin.manager.filter` createInstance chain | [node/3566774](https://www.drupal.org/node/3566774) |
| `ReplaceSessionManagerDeleteRector` | `SessionManager::delete($uid)` → `UserSessionRepositoryInterface::deleteAll($uid)` | [node/3570851](https://www.drupal.org/node/3570851) |
| `ClassConstantToClassConstantRector` | `CommentItemInterface::{FORM_BELOW,FORM_SEPARATE_PAGE,HIDDEN,CLOSED,OPEN}` and `CommentInterface::ANONYMOUS_*` → `FormLocation` / `CommentingStatus` / `AnonymousContact` enums | [node/3550054](https://www.drupal.org/node/3550054) |
| `FunctionToServiceRector` | `language_configuration_element_submit()` → `LanguageConfiguration::submit()` | [node/3574727](https://www.drupal.org/node/3574727) |
| `FunctionCallRemovalRector` | Remove `field_ui_form_manage_field_form_submit()`, `automated_cron_settings_submit()`, `syslog_facility_list()`, `syslog_logging_settings_submit()`, `taxonomy_build_node_index()`, `taxonomy_delete_node_index()`, views_ui contextual-suppress functions | [node/3566774](https://www.drupal.org/node/3566774) |
| `RemoveSetUriCallbackRector` | Remove `EntityTypeInterface::setUriCallback()` calls — use `link_templates` instead | [node/3575062](https://www.drupal.org/node/3575062) |
| `ReplaceRecipeRunnerInstallModuleRector` | `RecipeRunner::installModule($module)` → `installModules([$module])` | [node/3579527](https://www.drupal.org/node/3579527) |
| `ReplaceSystemPerformanceGzipKeyRector` | `system.performance` `css.gzip` / `js.gzip` config keys → `css.compress` / `js.compress` | [node/3526344](https://www.drupal.org/node/3526344) |
| `RemoveViewsRowCacheKeysRector` | Remove `CachePluginBase::getRowCacheKeys()` / `getRowId()` overrides | [node/3564958](https://www.drupal.org/node/3564958) |
| `RemoveCacheExpireOverrideRector` | Remove `CachePluginBase::cacheExpire()` subclass overrides | [node/3576855](https://www.drupal.org/node/3576855) |
| `RemoveTrustDataCallRector` | Strip `->trustData()` from `Config` fluent chains | [node/3348180](https://www.drupal.org/node/3348180) |
| `RemoveConfigSaveTrustedDataArgRector` | `Config::save($has_trusted_data)` — drop the boolean arg | [node/3348180](https://www.drupal.org/node/3348180) |
| `RemoveLinkWidgetValidateTitleElementRector` | Remove `LinkWidget::validateTitleElement()` — now handled by `LinkTitleRequiredConstraint` | [node/3554139](https://www.drupal.org/node/3554139) |
| `RemoveAutomatedCronSubmitHandlerRector` | Drop `'automated_cron_settings_submit'` from `$form['#submit']` — `#config_target` now handles config save | [node/3566774](https://www.drupal.org/node/3566774) |
| `ReplaceViewsProceduralFunctionsRector` | `views_view_is_{enabled,disabled}()`, `views_{enable,disable}_view()`, `views_get_view*()` → entity-storage / `Views::*` static calls | [node/3572594](https://www.drupal.org/node/3572594) |
| `GetOriginalClassToGetDecoratedClassesRector` | `EntityTypeInterface::getOriginalClass()` → `getDecoratedClasses()` array access | [node/3557464](https://www.drupal.org/node/3557464) |
| `UseEntityTypeHasIntegerIdRector` | `getEntityTypeIdKeyType() === 'integer'`, `entityTypeSupportsComments()`, `hasIntegerId($entityType)` → `EntityTypeInterface::hasIntegerId()` | [node/3566814](https://www.drupal.org/node/3566814) |
| `FunctionToServiceRector` | `editor_filter_xss()` → `EditorXssFilterInterface` service | [node/3568144](https://www.drupal.org/node/3568144) |
| `FunctionToStaticRector` | `_media_library_configure_form_display()` / `_media_library_configure_view_display()` → static helpers | [node/3566774](https://www.drupal.org/node/3566774) |
| `FunctionToStaticRector` | `language_configuration_element_submit()` static-call form → `LanguageConfiguration::submit()` | [node/3574727](https://www.drupal.org/node/3574727) |
| `FunctionToServiceRector` | `contextual_links_to_id()` / `contextual_id_to_links()` → `ContextualLinksSerializer` | [node/3568087](https://www.drupal.org/node/3568087) |
| `FunctionToServiceRector` | `views_add_contextual_links()` → `views.contextual_links` service | [node/3382344](https://www.drupal.org/node/3382344) |
| `ConstantToClassConstantRector` | `IMAGE_DERIVATIVE_TOKEN` → `ImageStyleInterface::TOKEN` | [node/3567619](https://www.drupal.org/node/3567619) |
| `ReplaceEntityReferenceRecursiveLimitRector` | `EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT` → literal `20` | [node/2940605](https://www.drupal.org/node/2940605) |
| `SystemRegionFunctionsRector` | `system_region_list()` / `system_default_region()` → `theme.region.manager` service | [node/3015812](https://www.drupal.org/node/3015812) |
| `CheckMarkupToProcessedTextRector` | `check_markup()` → processed-text render array | [node/3588040](https://www.drupal.org/node/3588040) |
| `RemoveFilterTipsLongParamRector` | `FilterInterface::tips()` — drop the `$long` parameter | [node/3567879](https://www.drupal.org/node/3567879) |
| `SystemSortThemesRector` | `'system_sort_themes'` string callback → `Closure` (closure callable) | [node/3566774](https://www.drupal.org/node/3566774) |
| `LocaleCompareIncToServiceRector` | `locale_translation_flush_projects()` / `locale_translation_build_projects()` / `locale_translation_check_projects*()` etc. → `locale.project` and `LocaleSource` services | [node/3037031](https://www.drupal.org/node/3037031) |

#### Drupal 10.3 deprecation rules

Wired in `config/drupal-10/drupal-10.3-deprecations.php`.

| Rule | What it does | Change record |
|---|---|---|
| `MethodToMethodWithCheckRector` | `RendererInterface::renderPlain()` → `renderInIsolation()` | [node/3407994](https://www.drupal.org/node/3407994) |
| `FunctionToStaticRector` | `file_icon_class()` → `IconMimeTypes::getIconClass()`; `file_icon_map()` → `IconMimeTypes::getGenericMimeType()` | [node/3411269](https://www.drupal.org/node/3411269) |
| `ReplaceRebuildThemeDataRector` | `ThemeHandlerInterface::rebuildThemeData()` → `extension.list.theme` service | [node/3413196](https://www.drupal.org/node/3413196) |
| `ReplaceModuleHandlerGetNameRector` | `ModuleHandlerInterface::getName()` → `extension.list.module` service | [node/3310017](https://www.drupal.org/node/3310017) |
| `ClassConstantToClassConstantRector` | `FileSystemInterface::EXISTS_*` → `\Drupal\Core\File\FileExists` enum | [node/3426517](https://www.drupal.org/node/3426517) |

#### Generic shared rectors

All eight live under `src/Rector/Deprecation/`. Each row labels what changed in
this release: `(new)` introduced in this PR, `(refactored)` existing class
restructured, `(enhanced)` new feature added, `(moved)` relocated from a
per-version folder, `(pre-existing)` unchanged but used by new D10.3 / D11
configs.

| Rector | Status in this release | Notes |
|---|---|---|
| `FunctionCallRemovalRector` | **(new)** | Removes deprecated function-call *statements* that have no direct replacement. Supports an accumulating configuration so per-version config files each contribute their own function list. |
| `ConstantToClassConstantRector` | **(refactored)** | Now extends `AbstractDrupalCoreRector` so it participates in BC wrapping when a `VersionedConfiguration` is supplied. Three previously-bespoke rectors (`ReplaceRequirementSeverityConstantsRector`, `ReplaceJsonApiFilterConstantsRector`, `ReplaceLocaleTranslationDefaultServerPatternRector`) were collapsed into config entries against this class. |
| `FunctionToServiceRector` | **(enhanced)** | New `useClassSyntax` option on `FunctionToServiceConfiguration` emits `\Drupal::service(ClassName::class)` instead of a string service ID. |
| `MethodToMethodWithCheckRector` | **(enhanced)** | Additional patterns matched (statement-level, assignment, fluent-chain) so it covers more configurations found during contrib-wide analysis. |
| `DrupalServiceRenameRector` | **(moved + bugfix)** | Lifted from `src/Drupal8/` into `src/Rector/Deprecation/`. The previous in-place `$node` mutation that broke the BC wrapper fallback is fixed — now clones before mutating, with a regression fixture. The legacy `Drupal8\Rector\Deprecation\DrupalServiceRenameRector` is kept as a thin subclass that re-validates the D8 configuration value object. |
| `FunctionToFirstArgMethodRector` | **(moved)** | Lifted from `src/Drupal9/` into `src/Rector/Deprecation/`. The legacy `Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector` is kept as a thin subclass. |
| `FunctionToStaticRector` | **(pre-existing)** | Unchanged class; new configurations added in D10.3 and D11.{1,2,3,4} configs. |
| `ClassConstantToClassConstantRector` | **(pre-existing)** | Unchanged class (introduced in PR #282); new configurations added in D10.3 (`FileSystemInterface::EXISTS_*` → `FileExists` enum) and D11.4 (`CommentItemInterface::*` enums). |

#### Tooling and scripts
- **`.claude/skills/rector-discover/SKILL.md`** — lists unimplemented
  drupal-digests rules by implementation phase; regenerates
  `docs/rector-index.yml` when stale.
- **`.claude/skills/rector-implement/SKILL.md`** — 14-step canonical conversion
  workflow from a digest rule to a finished rector class + test + fixture +
  config entry, with type-guard and version-gating quality gates.
- **`.claude/skills/rector-qa/SKILL.md`** — five-pass quality review (type
  guards, fixture coverage, BC decision correctness, `@see` URL accuracy,
  registration audit). Supports `all` mode for branch-wide scans.
- **`.claude/skills/rector-live-test/SKILL.md`** + `setup-rector-test.sh` —
  finds real contrib modules that use the deprecated API a rector targets and
  runs the rector against them to validate end-to-end. Uses
  [search.tresbien.tech](https://search.tresbien.tech) as primary source with
  GitLab API fallback.
- **`.claude/scripts/generate-rector-index.php`** — regenerates
  `docs/rector-index.yml` from the digests source.
- **`.claude/scripts/setup-repos.sh`** — clones `repos/drupal-digests` and
  `repos/drupal-core` for local development of new rectors.
- **`scripts/check-rector-coverage.php`** — evaluates real-world coverage by
  matching shipped rectors against real contrib patches.
- **`scripts/generate-deprecation-map.php`** — extracts `@trigger_error`
  deprecations from Drupal core into a structured YAML map.

### Changed


- `ClassConstantToClassConstantRector` and `MethodToMethodWithCheckRector` now
  extend `AbstractDrupalCoreRector` and auto-wrap their `Expr → Expr` rewrites
  via `DeprecationHelper::backwardsCompatibleCall()`. Their configuration value
  objects (`ClassConstantToClassConstantConfiguration`,
  `MethodToMethodWithCheckConfiguration`) gain an optional `introducedVersion`
  constructor argument (default `'0.0.0'`) and now implement
  `VersionedConfigurationInterface`. The default falls outside the BC-wrap
  gate (`< 10.0.0`), so consumers that instantiate these value objects without
  passing the new argument keep pre-refactor behaviour (no wrapping). This
  closes three D11 → D10 runtime regressions (Comment* enums in 11.4,
  `RequirementSeverity` in 11.2, `AliasManager` method rename in 11.1) without
  moving anything to a `-breaking.php` set.
- `MethodToMethodWithCheckRector` no longer attaches a "please confirm the
  receiver type" TODO comment for the `maybe` type-inference case. The
  comment-on-parent-statement mechanism relied on parent-node tracking that
  Rector 2.x removed. For configurations whose `introducedVersion ≥ 10.0.0`,
  the BC wrap selects the right call at runtime via `\Drupal::VERSION` and
  fully addresses the underlying concern. Configurations with an older
  introduced version (e.g. `urlInfo` → `toUrl` @ 8.0.0,
  `getLowercaseLabel` → `getSingularLabel` @ 8.8.0,
  `clearCsrfTokenSeed` → `stampNew` @ 9.2.0) fall outside the BC-wrap gate
  and rewrite unconditionally on a `maybe`-typed receiver. Contrib audit
  (api.tresbien.tech, 2026-05-28) found zero live callers of those three
  methods on receivers PHPStan cannot resolve to a concrete type, so the
  residual risk is theoretical.
- **Shipped `rector.php` disables BC wrapping by default.** New users get clean
  one-version rewrites out of the box. Contrib modules and projects that need to
  run on multiple Drupal versions should call `->enableBackwardCompatibility()`
  and optionally `->setMinimumCoreVersionSupported(...)` on the
  `DrupalRectorSettings` singleton.
  
  The `DrupalRectorSettings` class default for `backwardCompatibilityEnabled`
  remains `true` (avoids silently changing behaviour for existing users who do
  not call either method on the service); the shipped `rector.php` invokes
  `disableBackwardCompatibility()` explicitly to surface the project-level
  recommended default.
- **PHPUnit test setup** — tests now extend `AbstractDrupalRectorTestCase`
  instead of `AbstractRectorTestCase` directly so `DrupalRectorSettings`
  mutations don't leak between tests.
- **`composer.json` dev-dependency bumps**:
  - `phpunit/phpunit` `^10.0` → `^12.5.24`
  - `phpstan/phpstan` `^1.12 || ^2.0` → `^2.1.54`
  - `phpstan/phpstan-deprecation-rules` `^1.2 || ^2.0` → `^2.0.4`
  - `friendsofphp/php-cs-fixer` `^3.58` → `^3.95.1`
  - `symplify/vendor-patches` `^11.0` → `^12.0.6`
  - `cweagans/composer-patches` `^1.7.2` → `^2.0`
  - `symfony/yaml` `^5 || ^6 || ^7` → `^5 || ^6 || ^7.4.8`
- **CI matrix simplified** to PHP 8.3 + Rector 2 (previously also tested PHP 8.2
  + Rector 1).
- **Drupal stub `VERSION`** bumped from `10.99.x-dev` to `11.99.x-dev` so D11
  rules fire in tests by default; D11 tests opt out via
  `DrupalRectorSettings::setDrupalVersion('1.0.0')` for below-version cases.
- **Composer autoload `exclude-from-classmap`** entries added for
  `**/fixture/**`, `**/fixture-*/**`, `**/Fixture/**` so fixtures don't pollute
  consuming projects' class maps.

### Removed

- **Rector 1 support.** `composer.json` now requires `rector/rector:^2`. Drupal
  10 EOL is August 2026; Rector 1 was already EOL upstream.
- **`docs/rules_overview.md`** (1,210 lines, auto-generated). Replaced by
  on-demand generation via `.claude/scripts/generate-rector-index.php` →
  `docs/rector-index.yml`. The composer `docs` script that produced the file
  is also removed.
- Three single-purpose constant rectors collapsed into
  `ConstantToClassConstantRector` config entries:
  `ReplaceRequirementSeverityConstantsRector`,
  `ReplaceJsonApiFilterConstantsRector`,
  `ReplaceLocaleTranslationDefaultServerPatternRector`.

### Fixed

- **`DrupalServiceRenameRector` BC wrapper fallback.** `refactorWithConfiguration`
  used to mutate the input `$node` in place; the parent's `createBcCallOnExpr`
  reused that same already-mutated node as the deprecated branch of the
  `DeprecationHelper::backwardsCompatibleCall(...)`, so **both branches** ended
  up calling the renamed service and the legacy fallback was silently lost. Now
  clones before mutating; a regression fixture exercises the BC wrapping.
- **`ReplacePdoFetchConstantsRector` native-PDO receiver guard.** The original
  matcher rewrote `setFetchMode` / `fetch*` calls on **any** object whose
  argument was `\PDO::FETCH_*`, which would break native PDO statements. Now
  guards the receiver with
  `isObjectType($node->var, new ObjectType('Drupal\Core\Database\StatementInterface'))`.
- **`NodeStorageDeprecatedMethodsRector` missing `countDefaultLanguageRevisions`**
  — the statement-level REMOVE_NODE case was missing entirely; now covered.
- **`ReplaceEntityOriginalPropertyRector` nullsafe support** — added
  `NullsafePropertyFetch` to the node types and refactor logic so
  `$entity?->original` is rewritten to `$entity?->getOriginal()`.
- **`ReplaceEditorLoadRector` argument count** — added an arg-count guard so
  `editor_load()` (no args) and `editor_load($a, $b)` (two args) are not
  incorrectly transformed.
- **`RemoveCacheExpireOverrideRector`** — handles Time/Tag/None cache plugin
  subclasses, FQCN imports, aliased imports, and ignores side-effects in the
  method body.
- **8 unregistered rectors wired into deprecation configs** — eight new D11
  rector classes shipped with tests but no `config/` references in earlier
  drafts; now all wired into the matching `drupal-11.X-deprecations.php`.
- **`AbstractDrupalCoreRector::createBcCallOnExpr` visibility** — changed from
  private to protected so subclasses can call it directly when they have to
  build manual BC wraps for non-Expr top-level nodes (e.g.,
  `ReplacePdoFetchConstantsRector::refactor()`'s `ArrayItem` branch).

### Compatibility

| Dimension | Supported |
|---|---|
| **PHP** | `^8.2` (CI runs 8.3) |
| **Rector** | `^2` only — Rector 1 is dropped |
| **Drupal core** | 8, 9, 10, 11 (10 and 11 are the primary targets; 8 and 9 rules retained for legacy projects) |

### Upgrade notes

#### Refresh `rector.php`

The shipped `rector.php` was rewritten to register the `DrupalRectorSettings`
singleton. Refresh your project's copy:

```sh
cp vendor/palantirnet/drupal-rector/rector.php .
```

If you have local customisations, merge them by hand. The notable new block is:

```php
$rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
    (new DrupalRectorSettings())
        ->disableBackwardCompatibility()
);
```

#### Contrib modules running against an older minimum Drupal version

If your module must keep running on (e.g.) Drupal 10.5 while the development
environment runs 11.x, enable BC wrapping and tell the settings the minimum core
version you support so the wrappers are emitted correctly:

```php
$rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
    (new DrupalRectorSettings())
        ->enableBackwardCompatibility()
        ->setMinimumCoreVersionSupported('10.5.0')
);
```

#### Cleaning up old BC wrappers

Once you raise your module's minimum supported Drupal version, the existing
`DeprecationHelper::backwardsCompatibleCall()` wrappers in your code become
redundant. Strip them with `DeprecationHelperRemoveRector` (commented example in
the shipped `rector.php`):

```php
$rectorConfig->ruleWithConfiguration(DeprecationHelperRemoveRector::class, [
    new DeprecationHelperRemoveConfiguration('10.3.0'),
]);
```

This rewrites every wrapper whose `introducedVersion` is below `10.3.0` back to
the new API call directly.

#### Drupal 8 / 9 legacy rectors

Still included for legacy projects. The classes
`Drupal8\Rector\Deprecation\DrupalServiceRenameRector` and
`Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector` are thin subclasses
re-validating their D8/D9 configuration value objects; behaviour is unchanged.

[1.0.0-alpha1]: https://github.com/palantirnet/drupal-rector/releases/tag/1.0.0-alpha1
## [0.21.2] — 2026-05-08

### What's Changed
* build: fix codestyle by @bbrala in https://github.com/palantirnet/drupal-rector/pull/322
* fix: Fix phpstan issues and update pipeline actions to current versions by @bbrala in https://github.com/palantirnet/drupal-rector/pull/324
* Allow rector ^2.4.1 and replace file with getFile() if method exists by @samsonasik in https://github.com/palantirnet/drupal-rector/pull/326
* Add theme extension by @nlighteneddesign in https://github.com/palantirnet/drupal-rector/pull/325
* fix: fix theme hook test by @bbrala in https://github.com/palantirnet/drupal-rector/pull/328


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.21.1...0.21.2

[0.21.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.21.2

## [0.21.1] — 2025-11-07

### What's Changed
* Fix method not found on GetDeclaringSourceTrait::getDeclaringSource() by @samsonasik in https://github.com/palantirnet/drupal-rector/pull/317
* Update Deny list to include to missing hooks and remove preprocess by @nlighteneddesign in https://github.com/palantirnet/drupal-rector/pull/318
* Better replacement for dynamic uppercase parts in Implements hook_xy by @Berdir in https://github.com/palantirnet/drupal-rector/pull/320

### New Contributors
* @Berdir made their first contribution in https://github.com/palantirnet/drupal-rector/pull/320

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.21.0...0.21.1

[0.21.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.21.1

## [0.21.0] — 2025-05-23

This release adds Rector 2 support. FOr now we support both 1 and 2, for now this seems like a reasonable goal. If we start running into to many issues we will split the repo into a 1 and 2 release. 

### What's Changed
* feat: Drop Drupal 8 support by @bbrala in https://github.com/palantirnet/drupal-rector/pull/311
* feat: Add support for PHPstan 2 and Rector 2 by @ptmkenny in https://github.com/palantirnet/drupal-rector/pull/312
* feat: Add new HookConvert rector to convert legacy hooks to new OOP hooks. by @nlighteneddesign in https://github.com/palantirnet/drupal-rector/pull/308
* chore: Remove use of protected property NodeNameResolver on HookConvertRector by @samsonasik in https://github.com/palantirnet/drupal-rector/pull/316

### New Contributors
* @ptmkenny made their first contribution in https://github.com/palantirnet/drupal-rector/pull/312
* @nlighteneddesign made their first contribution in https://github.com/palantirnet/drupal-rector/pull/308
* @samsonasik made their first contribution in https://github.com/palantirnet/drupal-rector/pull/316

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.20.3...0.21.0

[0.21.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.21.0

## [0.20.3] — 2024-06-10

### New rectors
* New rector (10.3): Add rule for file_icon_class() and file_icon_map() by @timohuisman in https://github.com/palantirnet/drupal-rector/pull/304
* Add common PHPUnit 10 deprecations by @bbrala in https://github.com/palantirnet/drupal-rector/pull/307

### Bugfixes
* symplify/rule-doc-generator doesnt like cs-fixer a lot of times. Adju… by @bbrala in https://github.com/palantirnet/drupal-rector/pull/306

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.20.2...0.20.3

[0.20.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.20.3

## [0.20.2] — 2024-05-31

### New rectors
* New rector (10.1): drupal_theme_rebuild is deprecated  by @bbrala in https://github.com/palantirnet/drupal-rector/pull/297
* New rector (10.2): Rector for _drupal_flush_css_js through new VersionedFunctionToServiceRector by @bbrala in https://github.com/palantirnet/drupal-rector/pull/302

### Bugfixes
* bugfix: remove extra \ from Drupal\Core\StringTranslation\ByteSizeMarkup rector by @bbrala in https://github.com/palantirnet/drupal-rector/pull/301

### What's Changed
* Update core_plugin_conversion.md by @bbrala in https://github.com/palantirnet/drupal-rector/pull/298
* doc: Fix doc for AnnotationToAttributeRector and generate new docs. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/299
* Upgrade project tooling to PHP 8.2 by @agentrickard in https://github.com/palantirnet/drupal-rector/pull/300

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.20.1...0.20.2

[0.20.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.20.2

## [0.20.1] — 2024-03-09

This release adds the code to move from annotations to attributes which currently is being done in core. For now this code is not added by default until things settle down. If you want to use the rector check out [AnnotationToAttributeRector](https://github.com/palantirnet/drupal-rector/blob/main/src/Drupal10/Rector/Deprecation/AnnotationToAttributeRector.php).

If you want to contribute to core in the migration, [check out these docs](https://github.com/palantirnet/drupal-rector/blob/main/docs/core_plugin_conversion.md) or go help out in [#3396165](https://www.drupal.org/project/drupal/issues/3396165).

### New rectors
* New rector: Rule to convert action to attributes by @andypost in https://github.com/palantirnet/drupal-rector/pull/257
* New rector (9.1): ClassConstantToClassConstantRector to rename class constants to a new class. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/282
* feat: Optimize AnnotationToAttributeRector by @bbrala in https://github.com/palantirnet/drupal-rector/pull/296

### What's Changed
* Add bbrala to authors by @bbrala in https://github.com/palantirnet/drupal-rector/pull/294
* Add agentrickard to authors. by @agentrickard in https://github.com/palantirnet/drupal-rector/pull/295

### New Contributors
* @andypost made their first contribution in https://github.com/palantirnet/drupal-rector/pull/257

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.20.0...0.20.1

[0.20.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.20.1

## [0.20.0] — 2024-03-05

### New Rector

* feat: add rector rule for deprecated GDToolkit  by @timohuisman in https://github.com/palantirnet/drupal-rector/pull/289

### What's Changed
* refactor: Replace deprecated LevelSetLists for version specific PHPUnit, Symfony and Twig sets by @timohuisman in https://github.com/palantirnet/drupal-rector/pull/290
* feat: upgrade to rector 1.0 by @bbrala in https://github.com/palantirnet/drupal-rector/pull/292
* fix: Add proper levels for all symfony versions deprecated in the different versions of Drupal by @bbrala in https://github.com/palantirnet/drupal-rector/pull/293


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.19.2...0.20.0

[0.20.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.20.0

## [0.19.2] — 2024-01-17

### Fixed

* Fix: Double deprecated calls when already refactored by @bbrala in https://github.com/palantirnet/drupal-rector/pull/288


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.19.1...0.19.2

[0.19.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.19.2

## [0.19.1] — 2024-01-12

### What's Changed
* bugfix: switch arguments of AbstractDrupalCoreRector by @timohuisman in https://github.com/palantirnet/drupal-rector/pull/287


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.19.0...0.19.1

[0.19.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.19.1

## [0.19.0] — 2024-01-11

### New rector
* feat: Add rector rule for format_size in 10.2 by @timohuisman in https://github.com/palantirnet/drupal-rector/pull/286

### What's Changed
* feat: Upgrade rector to 0.19 and fix phpstan incompatiblities by @bbrala in https://github.com/palantirnet/drupal-rector/pull/284

### New Contributors
* @timohuisman made their first contribution in https://github.com/palantirnet/drupal-rector/pull/286

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.6...0.19.

[0.19.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.19.0

## [0.18.6] — 2023-12-28

### What's Changed
* Hotfix: New rector FILE_STATUS_PERMANENT was not added to deprecation list. … by @bbrala in https://github.com/palantirnet/drupal-rector/pull/280


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.5...0.18.6

[0.18.6]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.6

## [0.18.5] — 2023-12-28

### New rector
* New rector (9.3): Support FILE_STATUS_PERMANENT deprecation by @bbrala in https://github.com/palantirnet/drupal-rector/pull/278
* New rector (9.1): \Drupal\Component\Utility\Bytes::toInt() is deprecated by @bbrala in https://github.com/palantirnet/drupal-rector/pull/279


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.4...0.18.5

[0.18.5]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.5

## [0.18.4] — 2023-12-18

### New rector

* New rector: Support for module_load_include deprecation (Drupal 9) by @bbrala in https://github.com/palantirnet/drupal-rector/pull/277

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.3...0.18.4

[0.18.4]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.4

## [0.18.3] — 2023-12-06

### New rector
* PHPUnit: Rector to fix missing parent::setUp and parent::tearDown methods by @bbrala in https://github.com/palantirnet/drupal-rector/pull/273

### Bugfix
* Fix EntityManagerRector based on project_analysis results by @bbrala in https://github.com/palantirnet/drupal-rector/pull/274

### Other changes
* Remove latest release from readme, unneeded maintainance, its already… by @bbrala in https://github.com/palantirnet/drupal-rector/pull/270
* Better workflows by @bbrala in https://github.com/palantirnet/drupal-rector/pull/275


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.2...0.18.3

[0.18.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.3

## [0.18.2] — 2023-11-24

### New rector
* Add rector for system_time_zones by @bbrala in https://github.com/palantirnet/drupal-rector/pull/271

### What's Changed
* Link packagist version shield to package on packagist.org by @kasperg in https://github.com/palantirnet/drupal-rector/pull/269

### New Contributors
* @kasperg made their first contribution in https://github.com/palantirnet/drupal-rector/pull/269

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.1...0.18.2

[0.18.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.2

## [0.18.1] — 2023-11-21

### New rector
* Add $defaultTheme property if missing on BrowserTestBase by @mglaman in https://github.com/palantirnet/drupal-rector/pull/211

### What's Changed
* Update README.md by @bbrala in https://github.com/palantirnet/drupal-rector/pull/264
* Restructure rules by major and generate rule list (docs) by @bbrala in https://github.com/palantirnet/drupal-rector/pull/267
* Fix db_query.php expected test. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/268
* Update README-automated-testing.md by @bbrala in https://github.com/palantirnet/drupal-rector/pull/266
* Add php-cs-fixer by @bbrala in https://github.com/palantirnet/drupal-rector/pull/265
* Add $defaultTheme property if missing on BrowserTestBase by @mglaman in https://github.com/palantirnet/drupal-rector/pull/211


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.18.0...0.18.1

[0.18.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.1

## [0.18.0] — 2023-11-10

### Release highlights
This release of Drupal Rector introduces backwards compatible fixes for new rectors that target deprecations in Drupal 10. This is made possible by using the new `DeprecationHelper` class introduced in Drupal 10.1. 

We also worked on making the code more sustainable by employing `ConfigurableRectorInterface`. This interface allows passing configuration to a rector to set certain variables. This means it is easier to reuse rectors that for example rewrite a function to a static call without introducing new code.

Rector has also been upgraded from 0.15 to 0.18, bringing a lot of improvement, but also making it harder to add comments to code generated by Rector. This there are instances where the previous version added comments, that the current does not.

* Support for Drupal 10 deprecations and testing by @bbrala in https://github.com/palantirnet/drupal-rector/pull/252
* Support for backwards compatible rector rules and version scoping of rules by @bbrala in https://github.com/palantirnet/drupal-rector/pull/250

### Upgrade notes

Because rector moved to Laravel dependency injection a new version of `rector.php` must be copies/configured in your project root.

### New rectors
* Issue #3354343: Add TwigSetList::TWIG_240 to D9 deprecations. by @m4olivei in https://github.com/palantirnet/drupal-rector/pull/223
* Add new Rector for system_sort_modules_by_info_name() by @bbrala in https://github.com/palantirnet/drupal-rector/pull/253
* module_load_install() is deprecated in 9.4 and removed in 10. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/239
* Implement watchdog_exception rector by @bbrala in https://github.com/palantirnet/drupal-rector/pull/262
* 9.3 Multiple taxonomy rectors by @bbrala in https://github.com/palantirnet/drupal-rector/pull/254

### What's Changed
* Remove NodesToAddCollector  by @bbrala in https://github.com/palantirnet/drupal-rector/pull/225
* Remove dependency on rector-src by @bbrala in https://github.com/palantirnet/drupal-rector/pull/236
* Update PHPUnit configuration by @agentrickard in https://github.com/palantirnet/drupal-rector/pull/237
* Phase 1 - Refactor to support Rector 0.17 by @bbrala in https://github.com/palantirnet/drupal-rector/pull/238
* Simplify codebase: replace EntityLoadBase with configurable rule by @bbrala in https://github.com/palantirnet/drupal-rector/pull/228
* Refactor AssertLegacyTraitBase and ConstantToClassConstantBase to configurable rule by @bbrala in https://github.com/palantirnet/drupal-rector/pull/229
* Improve AssertNoFieldByIdRector by @mglaman in https://github.com/palantirnet/drupal-rector/pull/213
* Create FunctionToServiceRector to replace all function to service call deprecations by @bbrala in https://github.com/palantirnet/drupal-rector/pull/242
* New StaticToFunctionRector to replace StaticToFunctionBase by @bbrala in https://github.com/palantirnet/drupal-rector/pull/243
* Remove single use FunctionToImmutableConfigBase by @bbrala in https://github.com/palantirnet/drupal-rector/pull/245
* StaticArgumentRenameBase and DrupalServiceRenameBase are now covered with StaticArgumentRenameRector by @bbrala in https://github.com/palantirnet/drupal-rector/pull/241
* New ExtensionPathRector to replace ExtensionPathBase by @bbrala in https://github.com/palantirnet/drupal-rector/pull/244
* New DBRector to replace DbBase with configurable rector. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/246
* GetMockRector as configurable rule by @bbrala in https://github.com/palantirnet/drupal-rector/pull/248
* Upgrade to PHPStan level 6 by @bbrala in https://github.com/palantirnet/drupal-rector/pull/249
* MethodToMethodWithCheckRector to replace methods with a certainty check by @bbrala in https://github.com/palantirnet/drupal-rector/pull/247
* Refactor Base\FunctionToStatic to configurable rule. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/251
* Upgrade to Rector 0.18.x by @bbrala in https://github.com/palantirnet/drupal-rector/pull/240
* Refactored BC rules with configuration by @bbrala in https://github.com/palantirnet/drupal-rector/pull/255
* Add better labels to readme by @bbrala in https://github.com/palantirnet/drupal-rector/pull/256
* Add failing unit test for ExtensionPathRector when assining by @bbrala in https://github.com/palantirnet/drupal-rector/pull/258
* Add WatchdogExceptionRector the 10.1 setlist by @bbrala in https://github.com/palantirnet/drupal-rector/pull/263
* PHPUnit deprecations should be checked in Drupal 9 to 10 by @bbrala in https://github.com/palantirnet/drupal-rector/pull/261
* Fix MethodToMethodWithCheckRector by also matching on MethodCall. by @bbrala in https://github.com/palantirnet/drupal-rector/pull/260
* ExtensionPathRector does not handle Assignments by @bbrala in https://github.com/palantirnet/drupal-rector/pull/259

### New Contributors
* @m4olivei made their first contribution in https://github.com/palantirnet/drupal-rector/pull/223

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.15.1...0.18.0

[0.18.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.18.0

## [0.15.1] — 2023-03-23

### What's Changed
* Update rector config to resolve bootstrap issues with recent rector releases by @mglaman in https://github.com/palantirnet/drupal-rector/pull/220
* Update rector config to resolve bootstrap issues with recent rector releases by @goba in https://github.com/palantirnet/drupal-rector/pull/219


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.15.0...0.15.1

[0.15.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.15.1

## [0.15.0] — 2023-01-11

### What's Changed
* Update to rector 15 and github setup v3. by @agentrickard in https://github.com/palantirnet/drupal-rector/pull/218 and @FlorentTorregrosa in https://github.com/palantirnet/drupal-rector/pull/217
* Fix function signature in tests by @chrfritsch in https://github.com/palantirnet/drupal-rector/pull/216

### New Contributors
* @chrfritsch made their first contribution in https://github.com/palantirnet/drupal-rector/pull/216

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.13.1...0.15.0

[0.15.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.15.0

## [0.13.1] — 2022-08-17

### What's Changed
* ProtectedStaticModulesProperty rule by @mglaman in https://github.com/palantirnet/drupal-rector/pull/210
* Update rector.php so we do not use FQCN's in method arguments by @bbrala in https://github.com/palantirnet/drupal-rector/pull/209

### New Contributors
* @bbrala made their first contribution in https://github.com/palantirnet/drupal-rector/pull/209

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.13.0...0.13.1

[0.13.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.13.1

## [0.13.0] — 2022-07-15

### What's Changed
* #3295386 Bump to rector/rector:0.13.8 by @mglaman in https://github.com/palantirnet/drupal-rector/pull/204
* Remove unneeded assert for $pathValue by @mglaman in https://github.com/palantirnet/drupal-rector/pull/206
* Scope may not be available when detecting delcaring source by @mglaman in https://github.com/palantirnet/drupal-rector/pull/207
* Prevent @doesNotPerformAssertions from being added to tests by @mglaman in https://github.com/palantirnet/drupal-rector/pull/208


**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.12.4...0.13.0

[0.13.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.13.0

## [0.12.4] — 2022-06-01

### Release notes

* The 0.12.4 is a stable release pinned to Rector 0.12.21. Developers should be aware that Rector 0.12.22 introduces breaking changes to how we handle Drupal configuration.

* The 0.12.5 release of drupal-rector will include Rector 0.12.22. The upgrade path should be as simple as re-copying the configuration file. `cp vendor/palantirnet/drupal-rector/rector.php`

### What's Changed
* Add integration test for user_password() deprecation by @claudiu-cristea in https://github.com/palantirnet/drupal-rector/pull/201
* Issue #3282217: file_build_uri() is deprecated by @claudiu-cristea in https://github.com/palantirnet/drupal-rector/pull/202
* Remove remaining Rector conflicts up to 0.12.21 by @mglaman in https://github.com/palantirnet/drupal-rector/pull/203

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.12.3...0.12.4

[0.12.4]: https://github.com/palantirnet/drupal-rector/releases/tag/0.12.4

## [0.12.3] — 2022-05-24

The 0.12.3 release bypasses a known conflict with Rector 0.12.18. The current preferred version is Rector 0.12.19.

### What's Changed
* Fix PHP 8 warnings on `null` to `file_exists` by @mglaman in https://github.com/palantirnet/drupal-rector/pull/196
* Issue #3277704: Remove PHPUNIT_75 constant in Rector by @FlorentTorregrosa in https://github.com/palantirnet/drupal-rector/pull/197
* Issue #3280205: drupalPostForm() with 1st param NULL by @claudiu-cristea in https://github.com/palantirnet/drupal-rector/pull/198
* user_password() deprecation by @claudiu-cristea in https://github.com/palantirnet/drupal-rector/pull/199
* Remove conflicts on rector/rector by @mglaman in https://github.com/palantirnet/drupal-rector/pull/200

### New Contributors
* @claudiu-cristea made their first contribution in https://github.com/palantirnet/drupal-rector/pull/198

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.12.2...0.12.3

[0.12.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.12.3

## [0.12.2] — 2022-05-04

### What's Changed
* Mark conflict on rector >=0.12.18 by @mglaman in https://github.com/palantirnet/drupal-rector/pull/195
**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.12.1...0.12.2

[0.12.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.12.2

## [0.12.1] — 2022-02-21

Updates Drupal Rector for the most common Drupal 9.3 and 9.4 deprecations.

* https://www.drupal.org/project/rector/issues/3261614

### What's Changed

* Update composer.json for allow-plugins by @mglaman in https://github.com/palantirnet/drupal-rector/pull/186
* Rectors for drupal_get_path & drupal_get_filename by @mglaman in https://github.com/palantirnet/drupal-rector/pull/187
* Handle custom message types by @mglaman in https://github.com/palantirnet/drupal-rector/pull/190
* Add Rector for deprecated `render` by @mglaman in https://github.com/palantirnet/drupal-rector/pull/188
* Use dev-main for rector-src by @mglaman in https://github.com/palantirnet/drupal-rector/pull/193
* Rectors for file_move, file_copy, file_save_data by @mglaman in https://github.com/palantirnet/drupal-rector/pull/189
* file_url_generator service Rector rules by @mglaman in https://github.com/palantirnet/drupal-rector/pull/191
* MetadataBag::clearCsrfTokenSeed replaced by stampNew by @mglaman in https://github.com/palantirnet/drupal-rector/pull/192

**Full Changelog**: https://github.com/palantirnet/drupal-rector/compare/0.12.0...0.12.1

[0.12.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.12.1

## [0.12.0] — 2021-11-19

This release updates the codebase to support:

- PHPStan 1.0
- Rector 0.12.x

[0.12.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.12.0

## [0.11.4] — 2021-10-13

This is a maintenance release that includes updates to stay compatible with Rector.

[0.11.4]: https://github.com/palantirnet/drupal-rector/releases/tag/0.11.4

## [0.11.3] — 2021-09-03

This is a maintenance release that fixes a number of issues and updates the `deprecations-index` for Drupal 9 changes.

-     Cleans up internal function doc mismatches.
-     Fix entity_view(), entity_delete_multiple() and EntityTypeInterface::getLowercaseLabel deprecation message
-     Issue #3228110: Improve AssertNoUniqueTextRector documentation and scope
-     Adds AssertLegacy and other new items to the index.
-     Issue #3229896: Fix broken params on assert cache tag Rector rules
-     Ensure proper rector install on github ci.
-     Issue #3221584: Use "*" over sha for sandbox test
-     Issue #3228113: Make PassRector more specific and fix documentation
-     Prevents stubs used for testing from breaking end user's PHPUnit tests for Drupal
-     Fixes BuildXPathQuery docs.
-     Fix REQUEST_TIME deprecation message. Noted by @mglaman
-     Issue #3222671: Rector for assertNoFieldByName()
-     Issue #3222671: Rector for assertUniqueText() and assertNoUniqueText()
-     Issue #3222671: Rector for pass()

[0.11.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.11.3

## [0.11.2] — 2021-07-29

This release corrects an error in Rector 0.11.38 that was fixed in later versions (https://github.com/rectorphp/rector-src/pull/484).

See https://www.drupal.org/project/rector/issues/3225019 and a hat-tip to `Grimreaper` for reporting and testing this issue.

[0.11.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.11.2

## [0.11.1] — 2021-07-06

The release prepares drupal-rector to handle Drupal 9 deprecations, using PHP 8 and rector v 0.11.

This release also changes the file structure of the underlying code and introduced PHPUnit testing.

Note that this version can be run in PHP 7 without PHPUnit, which is not necessary for running rector upgrades on your code. 

This release supports 8.x and 9.x code conversions and can be run with either Drupal 8 or Drupal 9.

[0.11.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.11.1

## [0.11.0] — 2021-06-30

This release brings us up-to-date with Rector 11 and adds support for PHPUnit testing.

PHPUnit testing requires PHP 8 and is used for development.

Creating new Rector rules and running the Rector update requires PHP 7 or higher.

[0.11.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.11.0

## [0.10.0] — 2021-06-23

This release updates to Rector version 0.10.0 and prepares for more substantial changes coming to prepare for Drupal 10.

### Major changes

- We now use `rector.php` instead of `rector.yml` for configuration.
- Adds PHPStan for static code analysis.
- We are deprecating the Behat tests in favor of PHPUnit (See #152)

### New rules

- entity_view

[0.10.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.10.0

## [0.5.6] — 2020-06-05

Summary of updates in this release:

* 3 new Rector rules:
  * DatetimeDateStorageFormatRector
  * DatetimeDatetimeStorageFormatRector
  * DatetimeStorageTimezoneRector
* Upgrade to rector-prefixed 0.7.27
* Fix internal Github tests

[0.5.6]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.6

## [0.5.5] — 2020-05-30

Summary of updates in this release:
* Bug fixes for Rector rules
* Running the latest rector-prefixed
* Commented out PHPUnit8 code upgrade option in `rector.yml`
* [Drupal Rector rules documentation](https://github.com/palantirnet/drupal-rector/blob/master/docs/drupal_rector_rules.md)
* Add comments to call out edge cases in Drupal Rector code replacements (this option can be disabled through `rector.yml`)

[0.5.5]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.5

## [0.5.4] — 2020-05-22

Using latest rector-prefixed, which would stop creating diffs with unnecessary indentation fixes.

Added new Rector rules for the following deprecations:
* FILE_EXISTS_RENAME
* LinkGeneratorTrait::l()
* entity_create()
* SafeMarkup::format()

[0.5.4]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.4

## [0.5.3] — 2020-05-17

Added new Rector rules for the following deprecations:

* Unicode::strlen
* Unicode::substr
* EntityInterface:link()
* entity_load()
* node_load()
* file_load()
* file_directory_temp
* file_directory_os_temp
* drupal_realpath()
* file_uri_target()

[0.5.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.3

## [0.5.2] — 2020-05-09

Added new Rector rules for the following deprecations:

* db_update()
* file_scan_directory()
* REQUEST_TIME
* entity_get_display
* entity_get_form_display
* file_default_scheme()
* EntityInterface:urlInfo()

[0.5.2]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.2

## [0.5.1] — 2020-05-04

Added new Rector rules for the following deprecations:

* FILE_MODIFY_PERMISSIONS
* db_delete()

[0.5.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.1

## [0.5.0] — 2020-04-27

Added new Rector rules for the following deprecations:

* file_unmanaged_save_data()

[0.5.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.5.0

## [0.4.1] — 2020-04-25

Rector-prefix 0.7.19 is broken -
Issue:
rectorphp/rector#3256

PR to prevent it from happening in the future:
rectorphp/rector#3255 (comment)

[0.4.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.4.1

## [0.4.0] — 2020-04-17

Upgraded to latest Rector (version 0.7.x)

Added CI test using Github Actions

Added new Rector rules for the following deprecations:
* format_date
* Unicode::strtolower
* FILE_CREATE_DIRECTORY
* FILE_EXISTS_REPLACE
* Drupal::l()
* drupal_render()
* drupal_render_root()

[0.4.0]: https://github.com/palantirnet/drupal-rector/releases/tag/0.4.0

## [0.3.3] — 2020-04-11

Deprecations covered:
```
drupal_set_message()
entityManager()
  Drupal::entityManager()
  ControllerBase::entityManager()
db_insert
db_select
db_query
file_prepare_directory()
getMock()
  BrowserTestBase::getMock()
  KernelTestBase::getMock()
  UnitTestCase::getMock()
Drupal::url()
```

[0.3.3]: https://github.com/palantirnet/drupal-rector/releases/tag/0.3.3

## [0.3.1] — 2020-02-17

Renamed package to Drupal-Rector

[0.3.1]: https://github.com/palantirnet/drupal-rector/releases/tag/0.3.1

