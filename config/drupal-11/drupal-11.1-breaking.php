<?php

declare(strict_types=1);

/**
 * Drupal 11.1 "breaking" deprecation rules.
 *
 * See `drupal-11.4-breaking.php` for the full contract. In short: these are
 * `RenameClassRector` entries whose replacement class does not exist on every
 * drupal-rector-supported Drupal minor. The rewrite cannot be BC-wrapped (it
 * touches `use` / `extends` / `implements` / `::class`), so running it against
 * code that still needs to work on the missing minor will fatal there.
 *
 * NOT loaded by `drupal-11.1-deprecations.php` or `drupal-11-all-deprecations.php`.
 * Opt in via `Drupal11SetList::DRUPAL_111_BREAKING`.
 */

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3151086
    // https://www.drupal.org/node/3467559 (change record)
    // AliasWhitelist and AliasWhitelistInterface deprecated in drupal:11.1.0,
    // removed in drupal:12.0.0. Replaced by AliasPrefixList and
    // AliasPrefixListInterface, which were introduced in 11.1.0 and do NOT
    // exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\path_alias\AliasWhitelist' => 'Drupal\path_alias\AliasPrefixList',
        'Drupal\path_alias\AliasWhitelistInterface' => 'Drupal\path_alias\AliasPrefixListInterface',
    ]);
};
