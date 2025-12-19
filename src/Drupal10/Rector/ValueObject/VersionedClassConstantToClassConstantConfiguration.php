<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class VersionedClassConstantToClassConstantConfiguration implements VersionedConfigurationInterface
{
    protected string $deprecated;
    protected string $class;
    protected string $constant;

    protected string $deprecatedClass;

    protected string $introducedVersion;

    public function __construct(string $introducedVersion, string $deprecatedClass, string $deprecated, string $class, string $constant)
    {
        $this->introducedVersion = $introducedVersion;
        $this->deprecatedClass = $deprecatedClass;
        $this->deprecated = $deprecated;
        $this->class = $class;
        $this->constant = $constant;
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
