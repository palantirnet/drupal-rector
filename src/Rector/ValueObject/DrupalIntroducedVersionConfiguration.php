<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class DrupalIntroducedVersionConfiguration implements VersionedConfigurationInterface
{
    private string $introducedVersion;

    public function __construct(string $introducedVersion)
    {
        $this->introducedVersion = $introducedVersion;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }
}
