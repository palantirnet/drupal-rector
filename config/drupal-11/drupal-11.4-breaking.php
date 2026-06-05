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
    // https://www.drupal.org/node/3530640
    // https://www.drupal.org/node/3552724 (change record)
    // Drupal\user\Controller\UserAuthenticationController deprecated in
    // drupal:11.4.0, removed in drupal:12.0.0. Use
    // Drupal\rest\Controller\RestAuthenticationController instead. The
    // replacement controller was ADDED to the rest module in the same commit as
    // the deprecation (drupal-core c0e71efe93, on 11.x only) and does not exist
    // on any older minor, so rewriting `use` / `extends` / `::class` references
    // to it would produce a "class not found" fatal there. The rest module must
    // also be enabled at runtime for the new controller's routes to exist.
    //
    // This renames PHP references to the class only. Route name strings
    // (user.login.http → rest.login, user.logout.http → rest.logout,
    // user.login_status.http → rest.login_status, user.pass.http → rest.pass)
    // passed to Url::fromRoute() etc. are not touched and must be updated
    // manually.
    //
    // PHPSTAN_MESSAGES RenameClassRector: UserAuthenticationController is
    //   annotated `@deprecated in drupal:11.4.0` at the class level, so
    //   phpstan-deprecation-rules emits "Class ... extends deprecated class
    //   Drupal\user\Controller\UserAuthenticationController: ..." for subclasses
    //   and "Instantiation of deprecated class ..." for direct `new` calls.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\menu_link_content\Plugin\migrate\process\LinkOptions' => 'Drupal\migrate\Plugin\migrate\process\LinkOptions',
        'Drupal\menu_link_content\Plugin\migrate\process\LinkUri' => 'Drupal\migrate\Plugin\migrate\process\LinkUri',
        'Drupal\user\Controller\UserAuthenticationController' => 'Drupal\rest\Controller\RestAuthenticationController',
    ]);
};
