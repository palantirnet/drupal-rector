<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\DrupalServiceRenameConfiguration;

class DrupalServiceRenameRector extends \DrupalRector\Rector\Deprecation\DrupalServiceRenameRector
{
    public function configure(array $configuration): void
    {
        // This rule subclasses the generic DrupalServiceRenameRector, so Rector's
        // container also fires the generic rule's configuration callback on this
        // instance (afterResolving callbacks match by instanceof). Keep only our
        // own configuration; the generic rule instance applies the rest itself.
        $ownConfiguration = array_values(array_filter(
            $configuration,
            static fn ($value): bool => $value instanceof DrupalServiceRenameConfiguration
        ));

        if ($ownConfiguration === []) {
            return;
        }

        parent::configure($ownConfiguration);
    }
}
