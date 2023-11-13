<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\ValueObject;

class DrupalServiceRenameConfiguration
{
    protected string $newService;

    protected string $deprecatedService;

    public function __construct(string $deprecatedService, string $newService)
    {
        $this->deprecatedService = $deprecatedService;
        $this->newService = $newService;
    }

    public function getNewService(): string
    {
        return $this->newService;
    }

    public function getDeprecatedService(): string
    {
        return $this->deprecatedService;
    }
}
