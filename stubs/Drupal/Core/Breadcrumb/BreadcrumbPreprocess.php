<?php

declare(strict_types=1);

namespace Drupal\Core\Breadcrumb;

if (class_exists(\Drupal\Core\Breadcrumb\BreadcrumbPreprocess::class)) {
    return;
}

class BreadcrumbPreprocess
{
    public function preprocessBreadcrumb(&$variables): void {}
}
