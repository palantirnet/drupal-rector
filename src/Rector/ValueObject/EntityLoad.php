<?php

declare (strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use Rector\Core\Validation\RectorAssert;

final class EntityLoad
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
