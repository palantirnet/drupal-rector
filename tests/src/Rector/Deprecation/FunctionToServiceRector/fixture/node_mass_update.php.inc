<?php

function example($nids, $updates, $langcode) {
    node_mass_update($nids, $updates, $langcode, true, false);
}
?>
-----
<?php

function example($nids, $updates, $langcode) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('Drupal\node\NodeBulkUpdate')->process($nids, $updates, $langcode, true, false), fn() => node_mass_update($nids, $updates, $langcode, true, false));
}
?>
