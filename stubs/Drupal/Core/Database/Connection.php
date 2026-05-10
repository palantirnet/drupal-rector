<?php

declare(strict_types=1);

namespace Drupal\Core\Database;

if (class_exists(\Drupal\Core\Database\Connection::class)) {
    return;
}

class Connection {}
