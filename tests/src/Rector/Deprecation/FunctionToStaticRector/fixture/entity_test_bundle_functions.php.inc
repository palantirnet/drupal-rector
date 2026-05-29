<?php

function example_create() {
    entity_test_create_bundle($bundle, $text, $entity_type);
}

function example_delete() {
    entity_test_delete_bundle($bundle, $entity_type);
}
?>
-----
<?php

function example_create() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\entity_test\EntityTestHelper::createBundle($bundle, $text, $entity_type), fn() => entity_test_create_bundle($bundle, $text, $entity_type));
}

function example_delete() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\entity_test\EntityTestHelper::deleteBundle($bundle, $entity_type), fn() => entity_test_delete_bundle($bundle, $entity_type));
}
?>
