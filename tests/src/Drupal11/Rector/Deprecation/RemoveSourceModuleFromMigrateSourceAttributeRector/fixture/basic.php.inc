<?php

namespace Drupal\mymodule\Plugin\migrate\source;

use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

#[MigrateSource(
    id: 'd7_node',
    source_module: 'node',
)]
class Node extends DrupalSqlBase {}

?>
-----
<?php

namespace Drupal\mymodule\Plugin\migrate\source;

use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

#[MigrateSource(
    id: 'd7_node',
)]
class Node extends DrupalSqlBase {}

?>
