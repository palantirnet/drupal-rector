<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;
use Rector\Validation\RectorAssert;

final class ConstantToClassConfiguration implements VersionedConfigurationInterface
{
    private string $deprecated;
    private string $class;
    private string $constant;
    private string $introducedVersion;

    public function __construct(string $deprecated, string $class, string $constant, string $introducedVersion)
    {
        $this->deprecated = $deprecated;
        $this->class = $class;
        $this->constant = $constant;
        $this->introducedVersion = $introducedVersion;

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

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }
}
