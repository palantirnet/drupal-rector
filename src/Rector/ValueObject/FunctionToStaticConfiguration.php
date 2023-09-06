<?php

namespace DrupalRector\Rector\ValueObject;

class FunctionToStaticConfiguration {

    protected string $deprecatedFunctionName;

    protected string $className;

    protected string $methodName;

    /**
     * @param string $deprecatedFunctionName Deprecated function name
     * @param string $className Class to call static method on
     * @param string $methodName Method to call statically
     */
    public function __construct(string $deprecatedFunctionName, string $className, string $methodName) {
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->className = $className;
        $this->methodName = $methodName;
    }

    public function getDeprecatedFunctionName(): string {
        return $this->deprecatedFunctionName;
    }

    public function getClassName(): string {
        return $this->className;
    }

    public function getMethodName(): string {
        return $this->methodName;
    }
}
