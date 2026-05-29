<?php

node_access_rebuild();
node_access_rebuild(TRUE);
$needs = node_access_needs_rebuild();
node_access_needs_rebuild(TRUE);
node_access_needs_rebuild(FALSE);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeAccessRebuild::class)->rebuild(), fn() => node_access_rebuild());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeAccessRebuild::class)->rebuild(TRUE), fn() => node_access_rebuild(TRUE));
$needs = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeAccessRebuild::class)->needsRebuild(), fn() => node_access_needs_rebuild());
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeAccessRebuild::class)->setNeedsRebuild(TRUE), fn() => node_access_needs_rebuild(TRUE));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\node\NodeAccessRebuild::class)->setNeedsRebuild(FALSE), fn() => node_access_needs_rebuild(FALSE));
?>
