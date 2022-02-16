<?php

use Drupal\Core\Session\MetadataBag;
use Drupal\Core\Site\Settings;

function simple_example() {
    $metadata_bag = new MetadataBag(new Settings([]));
    $metadata_bag->clearCsrfTokenSeed();
}
