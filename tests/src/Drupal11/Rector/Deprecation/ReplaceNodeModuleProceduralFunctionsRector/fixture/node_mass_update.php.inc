<?php

node_mass_update($nids, $updates);
node_mass_update($nids, $updates, 'en', TRUE, FALSE);

?>
-----
<?php

\Drupal::service(\Drupal\node\NodeBulkUpdate::class)->process($nids, $updates);
\Drupal::service(\Drupal\node\NodeBulkUpdate::class)->process($nids, $updates, 'en', TRUE, FALSE);

?>
