<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use DrupalRector\Services\AddCommentService;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function() {
        return new AddCommentService();
    });

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90,
    ]);

    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        // https://www.drupal.org/node/3187914
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew'),
    ]);
};
