<?php

declare(strict_types=1);

use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;

function provideValidConstraint(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/core', '>=11.3.0');
}

function provideConstraintOfAnotherPackage(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/coder', '>=11.3.0');
}

function provideConstraintWithoutAPatchVersion(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/core', '^11.3');
}

function provideConstraintBoundedToTheRemovalMajor(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/core', '>=11.3.0 <13.0.0');
}

function provideConstraintRemovedMidMajor(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/core', '>=11.3.0 <12.1.0');
}

function provideConstraintWithAnInvertedSpan(): ComposerPackageConstraint
{
    return new ComposerPackageConstraint('drupal/core', '>=11.3.0 <11.0.0');
}
