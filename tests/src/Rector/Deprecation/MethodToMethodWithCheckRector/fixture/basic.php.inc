<?php

function simple_example() {
    $metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
    $metadata_bag->clearCsrfTokenSeed();
}
?>
-----
<?php

function simple_example() {
    $metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
    $metadata_bag->stampNew();
}
?>
