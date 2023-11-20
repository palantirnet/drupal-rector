<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

class FunctionToServiceConfiguration
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

    public function __construct(string $deprecatedFunctionName, string $serviceName, string $serviceMethodName)
    {
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->serviceName = $serviceName;
        $this->serviceMethodName = $serviceMethodName;
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
}
