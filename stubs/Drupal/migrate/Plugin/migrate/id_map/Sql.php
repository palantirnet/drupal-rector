<?php

declare(strict_types=1);

namespace Drupal\migrate\Plugin\migrate\id_map;

if (class_exists(\Drupal\migrate\Plugin\migrate\id_map\Sql::class)) {
    return;
}

class Sql {}
