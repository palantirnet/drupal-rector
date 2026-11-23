<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/config',
        __DIR__.'/scripts',
    ])
    ->exclude([
        'functional/hookconvertrector/fixture',
    ])
    // Adapted from Drupal core's tests/bootstrap.php, keep it close to upstream.
    ->notPath('drupal-phpunit-bootstrap-file.php')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'yoda_style' => false,
        'no_superfluous_phpdoc_tags' => false,
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true,
            'phpdoc_tags' => [],

        ],
    ])
    ->setFinder($finder)
    ;
