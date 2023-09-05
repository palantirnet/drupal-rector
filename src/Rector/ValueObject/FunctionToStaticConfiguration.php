<?php

namespace DrupalRector\Rector\ValueObject;

class FunctionToStaticConfiguration {

    protected string $deprecatedFunctionName = 'file_directory_os_temp';

    protected string $className = 'Drupal\Component\FileSystem\FileSystem';

    protected string $methodName = 'getOsTemporaryDirectory';

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
