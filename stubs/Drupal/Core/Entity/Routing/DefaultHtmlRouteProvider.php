<?php

declare(strict_types=1);

namespace Drupal\Core\Entity\Routing;

if (class_exists(\Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider::class)) {
    return;
}

class DefaultHtmlRouteProvider {}
