<?php

declare(strict_types=1);

namespace Drupal\Core\Config\Entity;

if (interface_exists(\Drupal\Core\Config\Entity\ConfigEntityInterface::class)) {
    return;
}

interface ConfigEntityInterface {}
