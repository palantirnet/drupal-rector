<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Drupal10\Rector\ValueObject\VersionedFunctionToServiceConfiguration;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class VersionedFunctionToServiceRector extends FunctionToServiceRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof VersionedFunctionToServiceConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', VersionedFunctionToServiceConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated function to service calls, used in Drupal 10 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
_drupal_flush_css_js();
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service('asset.query_string')->reset();
CODE_AFTER,
                [
                    new VersionedFunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
                ]
            ),
        ]);
    }
}
