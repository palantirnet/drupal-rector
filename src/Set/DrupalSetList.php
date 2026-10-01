<?php

declare(strict_types=1);

namespace DrupalRector\Set;

/**
 * Version-independent sets.
 *
 * @see \DrupalRector\Set\Drupal8SetList
 * @see \DrupalRector\Set\Drupal9SetList
 * @see \DrupalRector\Set\Drupal10SetList
 * @see \DrupalRector\Set\Drupal11SetList
 * @see \DrupalRector\Set\Drupal12SetList
 */
final class DrupalSetList
{
    /**
     * Every rule, each bound to the exact `drupal/core` version its deprecation
     * was introduced in.
     *
     * Rector activates only the rules whose constraint the installed
     * `drupal/core` satisfies, so this single set replaces hand-picking the
     * per-minor `Drupal*SetList` constants. Rules for a Drupal version that is
     * not installed never run, which is what makes the breaking renames safe to
     * include here.
     */
    public const COMPOSER_BASED = __DIR__.'/../../config/composer-based.php';
}
