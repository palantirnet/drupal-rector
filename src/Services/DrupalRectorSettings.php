<?php

declare(strict_types=1);

namespace DrupalRector\Services;

class DrupalRectorSettings
{
    private bool $backwardCompatibilityEnabled = false;

    private string $minimumCoreVersionSupported = '10.1.0';

    private ?string $drupalVersion = null;

    public function enableBackwardCompatibility(): static
    {
        $this->backwardCompatibilityEnabled = true;

        return $this;
    }

    public function disableBackwardCompatibility(): static
    {
        $this->backwardCompatibilityEnabled = false;

        return $this;
    }

    public function isBackwardCompatibilityEnabled(): bool
    {
        return $this->backwardCompatibilityEnabled;
    }

    public function setMinimumCoreVersionSupported(string $version): static
    {
        if ($version === '') {
            throw new \InvalidArgumentException('Minimum core version supported cannot be empty.');
        }

        $this->minimumCoreVersionSupported = $version;

        return $this;
    }

    public function getMinimumCoreVersionSupported(): string
    {
        return $this->minimumCoreVersionSupported;
    }

    public function setDrupalVersion(?string $version): static
    {
        $this->drupalVersion = $version;

        return $this;
    }

    public function getDrupalVersion(): ?string
    {
        return $this->drupalVersion;
    }
}
