<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class RenameClassRectorConfiguration implements VersionedConfigurationInterface
{
    private string $introducedVersion;

    private string $oldClass;

    private string $newClass;

    public function __construct(string $introducedVersion, string $oldClass, string $newClass)
    {
        $this->introducedVersion = $introducedVersion;
        $this->oldClass = $oldClass;
        $this->newClass = $newClass;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function getOldClass(): string
    {
        return $this->oldClass;
    }

    public function getNewClass(): string
    {
        return $this->newClass;
    }
}
