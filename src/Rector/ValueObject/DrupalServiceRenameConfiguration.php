<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class DrupalServiceRenameConfiguration implements VersionedConfigurationInterface
{
    protected string $introducedVersion;

    protected string $deprecatedService;

    protected string $newService;

    public function __construct(string $introducedVersion, string $deprecatedService, string $newService)
    {
        $this->introducedVersion = $introducedVersion;
        $this->deprecatedService = $deprecatedService;
        $this->newService = $newService;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function getDeprecatedService(): string
    {
        return $this->deprecatedService;
    }

    public function getNewService(): string
    {
        return $this->newService;
    }
}
