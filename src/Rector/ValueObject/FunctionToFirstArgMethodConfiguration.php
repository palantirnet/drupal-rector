<?php

namespace DrupalRector\Rector\ValueObject;

class FunctionToFirstArgMethodConfiguration {

    private string $deprecatedFunctionName;
    private string $methodName;

    /**
     * @param string $deprecatedFunctionName
     * @param string $methodName
     */
    public function __construct(string $deprecatedFunctionName, string $methodName) {
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->methodName = $methodName;
    }

    public function getDeprecatedFunctionName(): string {
        return $this->deprecatedFunctionName;
    }

    public function getMethodName(): string {
        return $this->methodName;
    }

}
