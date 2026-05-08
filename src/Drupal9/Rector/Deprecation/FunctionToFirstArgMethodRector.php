<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;

final class FunctionToFirstArgMethodRector extends \DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof FunctionToFirstArgMethodConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToFirstArgMethodConfiguration::class));
            }
        }

        parent::configure($configuration);
    }
}
