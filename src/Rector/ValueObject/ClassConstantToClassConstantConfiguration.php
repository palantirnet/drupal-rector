<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;
use Rector\Validation\RectorAssert;

final class ClassConstantToClassConstantConfiguration implements VersionedConfigurationInterface
{
    private string $deprecated;
    private string $class;
    private string $constant;

    private string $deprecatedClass;

    private string $introducedVersion;

    public function __construct(string $deprecatedClass, string $deprecated, string $class, string $constant, string $introducedVersion = '0.0.0')
    {
        $this->deprecatedClass = $deprecatedClass;
        $this->deprecated = $deprecated;
        $this->class = $class;
        $this->constant = $constant;
        $this->introducedVersion = $introducedVersion;

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

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }
}
