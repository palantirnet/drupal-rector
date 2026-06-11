<?php

namespace Drupal\mymodule\Plugin\migrate\source;

// A different "MigrateSource" attribute (NOT Drupal\migrate\Attribute\MigrateSource)
// that happens to accept a source_module argument must NOT be rewritten.
use Drupal\mymodule\Attribute\MigrateSource;

#[MigrateSource(
    id: 'custom',
    source_module: 'node',
)]
class Custom {}
