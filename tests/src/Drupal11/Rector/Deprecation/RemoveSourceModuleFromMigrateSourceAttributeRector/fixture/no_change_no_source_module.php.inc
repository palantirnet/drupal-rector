<?php

namespace Drupal\mymodule\Plugin\migrate\source;

use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

// A core MigrateSource attribute that never declared source_module — nothing
// to remove, left untouched (idempotent).
#[MigrateSource(
    id: 'my_source',
)]
class MySource extends SourcePluginBase {}
