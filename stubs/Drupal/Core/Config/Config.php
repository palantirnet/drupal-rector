<?php

declare(strict_types=1);

namespace Drupal\Core\Config;

if (class_exists(\Drupal\Core\Config\Config::class)) {
    return;
}

class Config {}
