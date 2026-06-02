<?php

declare(strict_types=1);

namespace DrupalRector\Set;

final class Drupal11SetList
{
    public const DRUPAL_11 = __DIR__.'/../../config/drupal-11/drupal-11-all-deprecations.php';
    public const DRUPAL_110 = __DIR__.'/../../config/drupal-11/drupal-11.0-deprecations.php';
    public const DRUPAL_111 = __DIR__.'/../../config/drupal-11/drupal-11.1-deprecations.php';
    public const DRUPAL_112 = __DIR__.'/../../config/drupal-11/drupal-11.2-deprecations.php';
    public const DRUPAL_113 = __DIR__.'/../../config/drupal-11/drupal-11.3-deprecations.php';
    public const DRUPAL_114 = __DIR__.'/../../config/drupal-11/drupal-11.4-deprecations.php';

    /**
     * Drupal 11.x "breaking" deprecation rules — opt-in sets.
     *
     * Each set holds `RenameClassRector` (or equivalent structural) entries
     * whose replacement symbol does not exist on every drupal-rector-supported
     * Drupal minor. The rewrite cannot be BC-wrapped, so applying it against
     * code that still needs to run on the older minor(s) will fatal there.
     *
     * NOT included in DRUPAL_11{X} or DRUPAL_11 — load explicitly only after
     * committing to drop support for the older minors named in the file's
     * docblock.
     */
    public const DRUPAL_111_BREAKING = __DIR__.'/../../config/drupal-11/drupal-11.1-breaking.php';
    public const DRUPAL_112_BREAKING = __DIR__.'/../../config/drupal-11/drupal-11.2-breaking.php';
    public const DRUPAL_113_BREAKING = __DIR__.'/../../config/drupal-11/drupal-11.3-breaking.php';
    public const DRUPAL_114_BREAKING = __DIR__.'/../../config/drupal-11/drupal-11.4-breaking.php';
}
