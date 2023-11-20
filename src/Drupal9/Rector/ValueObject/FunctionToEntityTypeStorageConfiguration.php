<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\ValueObject;

class FunctionToEntityTypeStorageConfiguration
{
    private string $entityTypeId;

    private string $entityStorageMethod;

    private string $deprecatedFunction;

    /**
     * @param string $deprecatedFunction
     * @param string $entityTypeId
     * @param string $entityStorageMethod
     */
    public function __construct(string $deprecatedFunction, string $entityTypeId, string $entityStorageMethod)
    {
        $this->entityTypeId = $entityTypeId;
        $this->deprecatedFunction = $deprecatedFunction;
        $this->entityStorageMethod = $entityStorageMethod;
    }

    public function getEntityTypeId(): string
    {
        return $this->entityTypeId;
    }

    public function getDeprecatedFunction(): string
    {
        return $this->deprecatedFunction;
    }

    public function getEntityStorageMethod(): string
    {
        return $this->entityStorageMethod;
    }
}
