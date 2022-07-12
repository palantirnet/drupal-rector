<?php declare(strict_types=1);

namespace DrupalRector\Set;

use Rector\Set\Contract\SetListInterface;

final class Drupal9SetList implements SetListInterface
{
    public const DRUPAL_9 = __DIR__ . '/../../config/drupal-9/drupal-9-all-deprecations.php';
    public const DRUPAL_90 = __DIR__ . '/../../config/drupal-9/drupal-9.0-deprecations.php';
    public const DRUPAL_91 = __DIR__ . '/../../config/drupal-9/drupal-9.1-deprecations.php';
    public const DRUPAL_92 = __DIR__ . '/../../config/drupal-9/drupal-9.2-deprecations.php';
    public const DRUPAL_93 = __DIR__ . '/../../config/drupal-9/drupal-9.3-deprecations.php';
}
