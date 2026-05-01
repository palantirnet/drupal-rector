<?php

declare(strict_types=1);

namespace Drupal\Core\Entity;

if (interface_exists(\Drupal\Core\Entity\EntityInterface::class)) {
    return;
}

interface EntityInterface {}
