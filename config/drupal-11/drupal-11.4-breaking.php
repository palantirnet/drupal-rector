<?php

declare(strict_types=1);

/**
 * Drupal 11.4 "breaking" deprecation rules.
 *
 * Rules in this file rewrite code into a form that does NOT run on every
 * drupal-rector-supported minor — typically because the replacement class /
 * symbol was *introduced together with* the deprecation and does not exist on
 * older minors. They cannot be BC-wrapped (most are class renames touching
 * `use` / `extends` / `implements` / `::class`, which is a structural change,
 * not an Expr → Expr rewrite).
 *
 * This file is NOT loaded by `drupal-11.4-deprecations.php` or
 * `drupal-11-all-deprecations.php`. Consumers must opt in explicitly by
 * loading `Drupal11SetList::DRUPAL_114_BREAKING` — typically only after
 * committing to drop support for any Drupal minor below the replacement's
 * introduced version.
 */

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3560075
    // https://www.drupal.org/node/3572239 (change record)
    // Drupal\menu_link_content\Plugin\migrate\process\LinkOptions and LinkUri
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0. The replacement
    // classes were ADDED to Drupal\migrate\Plugin\migrate\process in the same
    // commit as the deprecation (drupal-core 4b7913fb19a, on 11.x only) — they
    // do not exist on any Drupal 10.x branch. Running this rule against code
    // that still needs to work on Drupal 10 will produce a "class not found"
    // fatal there.
    // https://www.drupal.org/node/3581109
    // Drupal\help\Plugin\Search\HelpSearch moved out of the help module and
    // renamed to Drupal\search_help\Plugin\Search\SearchHelpSearch in the new
    // search_help core sub-module (drupal-core f55aee0362e, 11.4.0 via
    // system_update_11400()). The SearchHelpSearch class does not exist on any
    // Drupal minor below 11.4, so rewriting `use` / `::class` references to it
    // would produce a "class not found" fatal there.
    //
    // PHPSTAN_MESSAGES RenameClassRector: none. The old class was moved out of
    //   core entirely with no `class_alias` BC shim and no `@deprecated` alias
    //   left behind (verified: the `Drupal\help\Plugin\Search\HelpSearch` FQCN
    //   appears nowhere in 11.4 core), so phpstan-deprecation-rules emits no
    //   deprecation message — only a plain "class not found" once a site is on
    //   11.4. There is no message for upgrade_status to match against.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\menu_link_content\Plugin\migrate\process\LinkOptions' => 'Drupal\migrate\Plugin\migrate\process\LinkOptions',
        'Drupal\menu_link_content\Plugin\migrate\process\LinkUri' => 'Drupal\migrate\Plugin\migrate\process\LinkUri',
        'Drupal\help\Plugin\Search\HelpSearch' => 'Drupal\search_help\Plugin\Search\SearchHelpSearch',
    ]);
};
