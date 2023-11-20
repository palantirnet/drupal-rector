<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ValueObject;

/**
 * Configure minimum supported version for RemoveDeprecationHelperRector. All
 * calls to DeprecationHelper will be removed if the minimum supported version
 * is greater than the version that introduced the change.
 *
 * @see \DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector
 */
class DeprecationHelperRemoveConfiguration
{
    protected string $minimumRequirement;

    /**
     * @param string $minimumRequirement Minimum supported version
     */
    public function __construct(string $minimumRequirement)
    {
        $this->minimumRequirement = $minimumRequirement;
    }

    public function getMinimumRequirement(): string
    {
        return $this->minimumRequirement;
    }
}
