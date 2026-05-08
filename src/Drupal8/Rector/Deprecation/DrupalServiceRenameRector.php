<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\DrupalServiceRenameConfiguration;

class DrupalServiceRenameRector extends \DrupalRector\Rector\Deprecation\DrupalServiceRenameRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalServiceRenameConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalServiceRenameConfiguration::class));
            }
        }

        parent::configure($configuration);
    }
}
