<?php declare(strict_types=1);

namespace DrupalRector\Set;

use Rector\Set\Contract\SetListInterface;

final class Drupal8SetList implements SetListInterface
{
    public const DRUPAL_8 = __DIR__ . '/../../config/drupal-8/drupal-8-all-deprecations.php';
    public  const DRUPAL_80 = __DIR__ . '/../../config/drupal-8/drupal-8.0-deprecations.php';
    public  const DRUPAL_81 = __DIR__ . '/../../config/drupal-8/drupal-8.1-deprecations.php';
    public  const DRUPAL_82 = __DIR__ . '/../../config/drupal-8/drupal-8.2-deprecations.php';
    public  const DRUPAL_83 = __DIR__ . '/../../config/drupal-8/drupal-8.3-deprecations.php';
    public  const DRUPAL_84 = __DIR__ . '/../../config/drupal-8/drupal-8.4-deprecations.php';
    public  const DRUPAL_85 = __DIR__ . '/../../config/drupal-8/drupal-8.5-deprecations.php';
    public  const DRUPAL_86 = __DIR__ . '/../../config/drupal-8/drupal-8.6-deprecations.php';
    public  const DRUPAL_87 = __DIR__ . '/../../config/drupal-8/drupal-8.7-deprecations.php';
    public  const DRUPAL_88 = __DIR__ . '/../../config/drupal-8/drupal-8.8-deprecations.php';
}
