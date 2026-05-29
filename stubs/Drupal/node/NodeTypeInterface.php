<?php

declare(strict_types=1);

namespace Drupal\node;

if (interface_exists(\Drupal\node\NodeTypeInterface::class)) {
    return;
}

interface NodeTypeInterface {}
