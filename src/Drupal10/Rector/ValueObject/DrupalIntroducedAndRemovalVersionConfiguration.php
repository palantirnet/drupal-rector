<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class DrupalIntroducedAndRemovalVersionConfiguration implements VersionedConfigurationInterface
{
    private string $introducedVersion;

    private string $removeVersion;

    public function __construct(string $introducedVersion, string $removeVersion)
    {
        $this->introducedVersion = $introducedVersion;
        $this->removeVersion = $removeVersion;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function getRemoveVersion(): string
    {
        return $this->removeVersion;
    }
}
