<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;

final class FunctionToFirstArgMethodRector extends \DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector
{
    public function configure(array $configuration): void
    {
        // This rule subclasses the generic FunctionToFirstArgMethodRector, so
        // Rector's container also fires the generic rule's configuration callback
        // on this instance (afterResolving callbacks match by instanceof). When
        // both the Drupal 9 and Drupal 11 sets are loaded, the generic rule's
        // (version-tagged) configuration is delivered here too. Keep only our own
        // configuration; the generic rule instance applies the rest itself.
        $ownConfiguration = array_values(array_filter(
            $configuration,
            static fn ($value): bool => $value instanceof FunctionToFirstArgMethodConfiguration
        ));

        if ($ownConfiguration === []) {
            return;
        }

        parent::configure($ownConfiguration);
    }
}
