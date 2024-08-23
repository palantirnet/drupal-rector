<?php

declare(strict_types=1);

namespace DrupalRector\Set;

use Rector\Set\Contract\SetListInterface;

final class Drupal10SetList implements SetListInterface
{
    public const DRUPAL_10 = __DIR__.'/../../config/drupal-10/drupal-10-all-deprecations.php';
    public const DRUPAL_100 = __DIR__.'/../../config/drupal-10/drupal-10.0-deprecations.php';
    public const DRUPAL_101 = __DIR__.'/../../config/drupal-10/drupal-10.1-deprecations.php';
    public const DRUPAL_102 = __DIR__.'/../../config/drupal-10/drupal-10.2-deprecations.php';
    public const DRUPAL_103 = __DIR__.'/../../config/drupal-10/drupal-10.3-deprecations.php';
}
