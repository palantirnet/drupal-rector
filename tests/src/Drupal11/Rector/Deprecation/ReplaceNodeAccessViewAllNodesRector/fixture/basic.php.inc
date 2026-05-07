<?php

node_access_view_all_nodes();
node_access_view_all_nodes($account);
drupal_static_reset('node_access_view_all_nodes');
drupal_static_reset('other_function');

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants(\Drupal::currentUser()), fn() => node_access_view_all_nodes());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants($account), fn() => node_access_view_all_nodes($account));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('node.view_all_nodes_memory_cache')->deleteAll(), fn() => drupal_static_reset('node_access_view_all_nodes'));
drupal_static_reset('other_function');

?>
