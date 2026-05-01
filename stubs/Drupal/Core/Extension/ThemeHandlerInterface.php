<?php

declare(strict_types=1);

namespace Drupal\Core\Extension;

if (interface_exists(\Drupal\Core\Extension\ThemeHandlerInterface::class)) {
    return;
}

interface ThemeHandlerInterface {}
