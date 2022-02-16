<?php

namespace Drupal\Core\Session;

use Drupal\Core\Site\Settings;

if (class_exists('Drupal\Core\Session\MetadataBag')) {
    return;
}

class MetadataBag {
    public function __construct(Settings $settings) {

    }

    public function stampNew($lifetime = NULL) {

    }

    public function clearCsrfTokenSeed() {

    }
}
