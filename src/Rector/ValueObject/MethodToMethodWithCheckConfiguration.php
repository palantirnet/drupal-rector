<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

class MethodToMethodWithCheckConfiguration
{
    protected string $deprecatedMethodName;

    protected string $methodName;

    protected string $className;

    public function __construct(string $className, string $deprecatedMethodName, string $methodName)
    {
        $this->className = $className;
        $this->deprecatedMethodName = $deprecatedMethodName;
        $this->methodName = $methodName;
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
}
