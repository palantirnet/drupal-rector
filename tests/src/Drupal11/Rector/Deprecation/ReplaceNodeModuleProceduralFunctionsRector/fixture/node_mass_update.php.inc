<?php

node_mass_update($nids, $updates);
node_mass_update($nids, $updates, 'en', TRUE, FALSE);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\node\NodeBulkUpdate::class)->process($nids, $updates), fn() => node_mass_update($nids, $updates));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\node\NodeBulkUpdate::class)->process($nids, $updates, 'en', TRUE, FALSE), fn() => node_mass_update($nids, $updates, 'en', TRUE, FALSE));

?>
