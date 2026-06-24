<?php

declare(strict_types=1);

namespace Drupal\layout_discovery\Hook;

if (class_exists(\Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks::class)) {
    return;
}

class LayoutDiscoveryThemeHooks
{
    public function preprocessLayout(&$variables): void {}
}
