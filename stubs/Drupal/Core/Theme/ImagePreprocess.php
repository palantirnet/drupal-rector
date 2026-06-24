<?php

declare(strict_types=1);

namespace Drupal\Core\Theme;

if (class_exists(\Drupal\Core\Theme\ImagePreprocess::class)) {
    return;
}

class ImagePreprocess
{
    public function preprocessImage(&$variables): void {}
}
