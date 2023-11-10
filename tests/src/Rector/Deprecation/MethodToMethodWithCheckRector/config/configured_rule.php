<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(MethodToMethodWithCheckRector::class, $rectorConfig, TRUE, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl'),
    ]);
};
