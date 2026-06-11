<?php

declare(strict_types=1);

/**
 * Drupal 11.2 "breaking" deprecation rules.
 *
 * See `drupal-11.4-breaking.php` for the full contract. In short: these are
 * `RenameClassRector` entries whose replacement class does not exist on every
 * drupal-rector-supported Drupal minor. The rewrite cannot be BC-wrapped (it
 * touches `use` / `extends` / `implements` / `::class`), so running it against
 * code that still needs to work on the missing minor will fatal there.
 *
 * NOT loaded by `drupal-11.2-deprecations.php` or `drupal-11-all-deprecations.php`.
 * Opt in via `Drupal11SetList::DRUPAL_112_BREAKING`.
 */

use DrupalRector\Drupal11\Rector\Deprecation\RemoveSourceModuleFromMigrateSourceAttributeRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3009349
    // https://www.drupal.org/node/3306373 (change record)
    // The source_module constructor parameter was removed from
    // Drupal\migrate\Attribute\MigrateSource in drupal:11.2.0. Passing
    // #[MigrateSource(source_module: '...')] raises an "Unknown named
    // parameter" error at plugin discovery on 11.2.0+. This rule strips the
    // source_module named argument from #[MigrateSource] usages.
    //
    // Non-BC: an Attribute is not an Expr → Expr transformation, so it cannot
    // be BC-wrapped, and the argument is mutually exclusive across minors —
    // keeping it fatals on 11.2.0+, removing it drops the requirement metadata
    // DrupalSqlBase uses to enforce the source module for D6/D7 migrations on
    // older Drupal. For DrupalSqlBase plugins the value must be re-declared via
    // the @MigrateSource annotation or the migration YAML after removal; that
    // is a manual follow-up this rule does not automate. Apply only after
    // dropping support for Drupal minors that predate 11.2.0.
    //
    // TODO PHPSTAN_MESSAGES RemoveSourceModuleFromMigrateSourceAttributeRector:
    //   none. source_module was hard-removed (not @deprecated), so phpstan-
    //   deprecation-rules has no deprecation to report. Verified against a
    //   Drupal 11 test env: phpstan instead emits the hard error "Unknown
    //   parameter $source_module in call to Drupal\migrate\Attribute\
    //   MigrateSource constructor." (argument.unknownParameter, not a
    //   deprecation). Intentionally no coverage message.
    $rectorConfig->rule(RemoveSourceModuleFromMigrateSourceAttributeRector::class);

    // https://www.drupal.org/node/3488572
    // https://www.drupal.org/node/3488580 (change record)
    // Drupal\Core\Entity\Query\Sql\pgsql\* deprecated in drupal:11.2.0,
    // removed in drupal:12.0.0. Moved to Drupal\pgsql\EntityQuery\*, which
    // does not exist on any Drupal 10.x branch.
    //
    // (3472008 / jsonapi ResourceResponseValidator: intentionally NOT included.
    // The "replacement" is a class inside core/modules/jsonapi/tests/modules/ —
    // a core test module that only resolves when explicitly enabled. Rewriting
    // every reference to the production FQCN into the test-module FQCN would
    // fatal on D10 AND on any production D11 site that does not enable that
    // test module. There is no version of "breaking" that makes this rename
    // safe at runtime, so the rule is not shipped.)
    //
    // https://www.drupal.org/node/3498915
    // https://www.drupal.org/node/3498916 (change record)
    // Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity[Deriver]
    // deprecated in drupal:11.2.0, removed in drupal:12.0.0. Moved to
    // Drupal\migrate\Plugin\migrate\source\*, which does not exist on any
    // Drupal 10.x branch.
    //
    // https://www.drupal.org/node/3258581
    // https://www.drupal.org/node/3439256 (change record)
    // Drupal\content_translation\Plugin\migrate\source\I18nQueryTrait
    // deprecated in drupal:11.2.0, removed in drupal:12.0.0. Moved to
    // Drupal\migrate_drupal\Plugin\migrate\source\I18nQueryTrait, which does
    // not exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\Core\Entity\Query\Sql\pgsql\QueryFactory' => 'Drupal\pgsql\EntityQuery\QueryFactory',
        'Drupal\Core\Entity\Query\Sql\pgsql\Condition' => 'Drupal\pgsql\EntityQuery\Condition',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity' => 'Drupal\migrate\Plugin\migrate\source\ContentEntity',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntityDeriver' => 'Drupal\migrate\Plugin\migrate\source\ContentEntityDeriver',
        'Drupal\content_translation\Plugin\migrate\source\I18nQueryTrait' => 'Drupal\migrate_drupal\Plugin\migrate\source\I18nQueryTrait',
    ]);
};
