<?php

$grants = node_access_grants($operation, $account);
?>
-----
<?php

$grants = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeGrantsHelper::class)->nodeAccessGrants($operation, $account), fn() => node_access_grants($operation, $account));
?>
