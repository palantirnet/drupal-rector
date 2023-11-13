<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\ValueObject;

class ExtensionPathConfiguration
{
    private string $functionName;

    private string $methodName;

    public function __construct(string $functionName, string $methodName)
    {
        $this->functionName = $functionName;
        $this->methodName = $methodName;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
