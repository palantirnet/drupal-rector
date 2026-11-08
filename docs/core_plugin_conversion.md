# Core annotation to attribute conversion

To convert a plugin in core from an annotation to an attribute, you need to do the following after checking out core.

## Install drupal-rector

Adding `-W` since PHPStan needs updating.

```bash
composer require --dev palantirnet/drupal-rector -W
```

## Configure drupal-rector (rector.php)

Create the following file in the root of core. In this example we are converting the `ContentEntityType` and `ConfigEntityType` annotations to attributes. If you want to convert other annotations, you will need to configure the `AnnotationToAttributeRector` with the appropriate `AnnotationToAttributeConfiguration` objects.

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(
        \DrupalRector\Drupal10\Rector\Deprecation\AnnotationToAttributeRector::class,
        [
            // Setting both introduce and remove version to 10.x means the
            // comments are not kept. Which is good for core. ;)
            new \DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration(
                '10.0.0',
                '10.0.0',
                'ContentEntityType',
                'Drupal\Core\Entity\Attribute\ContentEntityType',
            ),
            new \DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration(
                '10.0.0',
                '10.0.0',
                'ConfigEntityType',
                'Drupal\Core\Entity\Attribute\ConfigEntityType',
            ),
        ],
    )
    ->withAutoloadPaths([
        './core/lib',
        './core/modules',
        './core/profiles',
        './core/themes',
    ])
    ->withSkip([
        '*/upgrade_status/tests/modules/*',
        '*/ProxyClass/*',
        '*/tests/fixtures/*',
        '*/vendor/*',
    ])
    ->withFileExtensions([
        'php',
        'module',
        'theme',
        'install',
        'profile',
        'inc',
        'engine',
    ])
    ->withImportNames(
        importNames: false,
        importDocBlockNames: false,
        importShortClasses: false,
        removeUnusedImports: false,
    );
```

## Running rector against core

Running will take a while. You can run against specific directories like so:

```bash
vendor/bin/rector process ./core/lib
vendor/bin/rector process ./core/modules
vendor/bin/rector process ./core/themes
```

Or run against specific modules:

```bash
vendor/bin/rector process ./core/modules/system
vendor/bin/rector process ./core/modules/user
```

Or if you have horsepower, run against the whole of core:

```bash
vendor/bin/rector process ./core
```

## Review the changes

Always review the changes. Rector is a tool to help you, not to do the work for you. It will not be able to convert everything, and it may make mistakes. Make sure you understand the changes and that they are correct.

## Reporting errors

If you find an error, please report it to the rector project. You can do this by creating an issue in the rector project on Drupal.org.

We are also available on the #rector channel on Drupal Slack.

@bbrala @mglaman @agentrickard
