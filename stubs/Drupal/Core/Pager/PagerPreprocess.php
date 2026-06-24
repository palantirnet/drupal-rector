<?php

declare(strict_types=1);

namespace Drupal\Core\Pager;

if (class_exists(\Drupal\Core\Pager\PagerPreprocess::class)) {
    return;
}

class PagerPreprocess
{
    public function preprocessPager(&$variables): void {}
}
