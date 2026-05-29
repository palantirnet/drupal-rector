<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class FunctionToFirstArgMethodConfiguration implements VersionedConfigurationInterface
{
    protected string $introducedVersion;

    private string $deprecatedFunctionName;

    private string $methodName;

    public function __construct(string $introducedVersion, string $deprecatedFunctionName, string $methodName)
    {
        $this->introducedVersion = $introducedVersion;
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->methodName = $methodName;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function getDeprecatedFunctionName(): string
    {
        return $this->deprecatedFunctionName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
