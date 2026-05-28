<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class MethodToMethodWithCheckConfiguration implements VersionedConfigurationInterface
{
    protected string $deprecatedMethodName;

    protected string $methodName;

    protected string $className;

    protected string $introducedVersion;

    public function __construct(string $className, string $deprecatedMethodName, string $methodName, string $introducedVersion)
    {
        $this->className = $className;
        $this->deprecatedMethodName = $deprecatedMethodName;
        $this->methodName = $methodName;
        $this->introducedVersion = $introducedVersion;
    }

    public function getDeprecatedMethodName(): string
    {
        return $this->deprecatedMethodName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }
}
