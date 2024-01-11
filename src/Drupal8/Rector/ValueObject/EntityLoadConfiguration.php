<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\ValueObject;

use Rector\Validation\RectorAssert;

final class EntityLoadConfiguration
{
    private string $entityType;

    public function __construct(string $entityType = 'entity')
    {
        $this->entityType = $entityType;

        RectorAssert::functionName($entityType);
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }
}
