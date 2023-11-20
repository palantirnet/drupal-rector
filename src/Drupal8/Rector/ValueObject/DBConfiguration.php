<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\ValueObject;

class DBConfiguration
{
    private string $deprecatedMethodName;

    private int $optionsArgumentPosition;

    public function __construct(string $deprecatedMethodName, int $optionsArgumentPosition)
    {
        $this->deprecatedMethodName = $deprecatedMethodName;
        $this->optionsArgumentPosition = $optionsArgumentPosition;
    }

    public function getDeprecatedMethodName(): string
    {
        return $this->deprecatedMethodName;
    }

    public function getOptionsArgumentPosition(): int
    {
        return $this->optionsArgumentPosition;
    }
}
