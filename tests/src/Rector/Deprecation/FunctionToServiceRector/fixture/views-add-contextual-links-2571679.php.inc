<?php

views_add_contextual_links($element, 'view', $display_id);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\views\ContextualLinksHelper::class)->addLinks($element, 'view', $display_id), fn() => views_add_contextual_links($element, 'view', $display_id));
?>
