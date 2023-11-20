<?php

declare(strict_types=1);

namespace DrupalRector\Contract;

interface VersionedConfigurationInterface
{
    public function getIntroducedVersion(): string;
}
