<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(MethodToMethodWithCheckRector::class, $rectorConfig, true, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'getResource', 'getImage'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'setResource', 'setImage'),
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Cache\CacheBackendInterface', 'invalidateAll', 'deleteAll'),
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Render\RendererInterface', 'renderPlain', 'renderInIsolation'),
    ]);
};
