<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use Rector\Validation\RectorAssert;

final class ClassConstantToClassConstantConfiguration
{
    private string $deprecated;
    private string $class;
    private string $constant;

    private string $deprecatedClass;

    public function __construct(string $deprecatedClass, string $deprecated, string $class, string $constant)
    {
        $this->deprecatedClass = $deprecatedClass;
        $this->deprecated = $deprecated;
        $this->class = $class;
        $this->constant = $constant;

        RectorAssert::className($deprecatedClass);
        RectorAssert::className($class);
        RectorAssert::constantName($deprecated);
        RectorAssert::constantName($constant);
    }

    public function getDeprecated(): string
    {
        return $this->deprecated;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getConstant(): string
    {
        return $this->constant;
    }

    public function getDeprecatedClass(): string
    {
        return $this->deprecatedClass;
    }
}
