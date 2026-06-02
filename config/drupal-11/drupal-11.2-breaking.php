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

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
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
