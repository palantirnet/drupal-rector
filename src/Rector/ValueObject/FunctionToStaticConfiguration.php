<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class FunctionToStaticConfiguration implements VersionedConfigurationInterface
{
    protected string $deprecatedFunctionName;

    protected string $className;

    protected string $methodName;

    /**
     * @var array|int[] Reorder arguments array[old_position] = new_position
     */
    private array $argumentReorder;

    private string $introducedVersion;

    /**
     * @param string      $introducedVersion      Introduced version of this change
     * @param string      $deprecatedFunctionName Deprecated function name
     * @param string      $className              Class to call static method on
     * @param string      $methodName             Method to call statically
     * @param array|int[] $argumentReorder        Reorder arguments array[old_position] = new_position
     */
    public function __construct(string $introducedVersion, string $deprecatedFunctionName, string $className, string $methodName, array $argumentReorder = [])
    {
        $this->deprecatedFunctionName = $deprecatedFunctionName;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->argumentReorder = $argumentReorder;
        $this->introducedVersion = $introducedVersion;
    }

    public function getDeprecatedFunctionName(): string
    {
        return $this->deprecatedFunctionName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return array|int[] Reorder arguments array[old_position] = new_position
     */
    public function getArgumentReorder(): array
    {
        return $this->argumentReorder;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }
}
