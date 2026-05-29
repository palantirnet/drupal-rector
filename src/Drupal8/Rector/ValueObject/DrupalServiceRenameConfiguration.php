<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\ValueObject;

use DrupalRector\Rector\ValueObject\DrupalServiceRenameConfiguration as GenericDrupalServiceRenameConfiguration;

class DrupalServiceRenameConfiguration extends GenericDrupalServiceRenameConfiguration
{
    public function __construct(string $deprecatedService, string $newService)
    {
        parent::__construct('', $deprecatedService, $newService);
    }
}
