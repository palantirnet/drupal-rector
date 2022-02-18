<?php

declare(strict_types=1);

namespace Drupal\Core;

if (class_exists('Drupal\Core\Url')) {
    return;
}

class Url {
    public static function fromUri($uri, $options = []): self {

    }
}
