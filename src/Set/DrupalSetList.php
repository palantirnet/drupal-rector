<?php

declare(strict_types=1);

namespace DrupalRector\Set;

final class DrupalSetList
{
    /**
     * Picks the Drupal sets from the installed `drupal/core` version, see
     * \DrupalRector\Set\ComposerBasedSetResolver.
     *
     * Loaded by Rector's `withComposerBased(drupal: true)`, or directly with
     * `withSets([DrupalSetList::COMPOSER_BASED])`.
     */
    public const COMPOSER_BASED = __DIR__.'/../../config/composer-based.php';
}
