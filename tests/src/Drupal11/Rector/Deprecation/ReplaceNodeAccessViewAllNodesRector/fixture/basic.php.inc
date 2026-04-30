<?php

node_access_view_all_nodes();
node_access_view_all_nodes($account);
drupal_static_reset('node_access_view_all_nodes');
drupal_static_reset('other_function');

?>
-----
<?php

\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants(\Drupal::currentUser());
\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants($account);
\Drupal::service('node.view_all_nodes_memory_cache')->deleteAll();
drupal_static_reset('other_function');

?>
