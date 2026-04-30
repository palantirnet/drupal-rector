<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

class FunctionCallRemovalConfiguration
{
    public function __construct(private readonly string $functionName)
    {
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}
