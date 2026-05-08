<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class FunctionToServiceConfiguration implements VersionedConfigurationInterface
{
    /**
     * The deprecated function name.
     */
    protected string $deprecatedFunctionName;

    /**
     * The replacement service name.
     */
    protected string $serviceName;

    /**
     * The replacement service method.
     */
    protected string $serviceMethodName;

    protected string $introducedVersion;

    protected bool $useClassSyntax;

    public function __construct(string $introducedVersion, string $deprecatedFunctionName, string $serviceName, string $serviceMethodName, bool $useClassSyntax = false)
    {
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->serviceName = $serviceName;
        $this->serviceMethodName = $serviceMethodName;
        $this->introducedVersion = $introducedVersion;
        $this->useClassSyntax = $useClassSyntax;
    }

    public function getDeprecatedFunctionName(): string
    {
        return $this->deprecatedFunctionName;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getServiceMethodName(): string
    {
        return $this->serviceMethodName;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function useClassSyntax(): bool
    {
        return $this->useClassSyntax;
    }
}
