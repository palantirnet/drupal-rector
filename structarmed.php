<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Preset\Presets\Psr4Preset;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Contract', 'src/Contract/')
    ->layer('Services', 'src/Services/')
    ->layer('Utility', 'src/Utility/')
    ->layer('Set', 'src/Set/')
    ->layer('ValueObject', [
        'src/Rector/ValueObject/',
        'src/Rector/PHPUnit/ValueObject/',
        'src/Drupal8/Rector/ValueObject/',
        'src/Drupal9/Rector/ValueObject/',
        'src/Drupal10/Rector/ValueObject/',
    ])
    // version-agnostic rectors and their base classes
    ->layer('CoreRector', 'src/Rector/', [
        'src/Rector/ValueObject/',
        'src/Rector/PHPUnit/ValueObject/',
    ])
    // version-specific rectors, isolated from each other
    ->layer('Drupal8Rector', 'src/Drupal8/Rector/', 'src/Drupal8/Rector/ValueObject/')
    ->layer('Drupal9Rector', 'src/Drupal9/Rector/', 'src/Drupal9/Rector/ValueObject/')
    ->layer('Drupal10Rector', 'src/Drupal10/Rector/', 'src/Drupal10/Rector/ValueObject/')
    ->layer('Drupal11Rector', 'src/Drupal11/Rector/')
    ->layer('Drupal12Rector', 'src/Drupal12/Rector/')
    ->ruleset([
        'Contract' => [],
        'Services' => [],
        'Utility' => [],
        'Set' => [],
        'ValueObject' => ['Contract'],
        'CoreRector' => ['+ValueObject', 'Services'],
        'Drupal8Rector' => ['+CoreRector', 'Utility'],
        'Drupal9Rector' => ['+CoreRector', 'Utility'],
        'Drupal10Rector' => ['+CoreRector', 'Utility'],
        'Drupal11Rector' => ['+CoreRector', 'Utility'],
        'Drupal12Rector' => ['+CoreRector', 'Utility'],
    ])
    ->skip([
        // multiple classes on single file
        Psr4Preset::CLASSES_MUST_MATCH_COMPOSER => [
            __DIR__ . '/tests/src/Drupal10/Rector/Deprecation/ActionAnnotationToAttributeRector/BackwardsCompatibilityActionAnnotationToAttributeRectorTest.php',
        ],
    ]);
