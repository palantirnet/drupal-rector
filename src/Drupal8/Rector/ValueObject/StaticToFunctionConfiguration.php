<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\ValueObject;

class StaticToFunctionConfiguration
{
    private string $deprecatedFullyQualifiedClassName;

    private string $deprecatedMethodName;

    private string $functionName;

    public function __construct(string $deprecatedFullyQualifiedClassName, string $deprecatedMethodName, string $functionName)
    {
        $this->deprecatedFullyQualifiedClassName = $deprecatedFullyQualifiedClassName;
        $this->deprecatedMethodName = $deprecatedMethodName;
        $this->functionName = $functionName;
    }

    public function getDeprecatedFullyQualifiedClassName(): string
    {
        return $this->deprecatedFullyQualifiedClassName;
    }

    public function getDeprecatedMethodName(): string
    {
        return $this->deprecatedMethodName;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}
