<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\Base\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function(\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90,
    ]);

    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        // https://www.drupal.org/node/3187914
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew'),
    ]);
};
