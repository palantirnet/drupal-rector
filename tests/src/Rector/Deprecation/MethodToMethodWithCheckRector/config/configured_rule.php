<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(MethodToMethodWithCheckRector::class, $rectorConfig, false, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew', '9.2.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl', '8.0.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'getResource', 'getImage', '10.2.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'setResource', 'setImage', '10.2.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild', '11.1.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Cache\CacheBackendInterface', 'invalidateAll', 'deleteAll', '11.2.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Render\RendererInterface', 'renderPlain', 'renderInIsolation', '10.3.0'),
    ]);
};
