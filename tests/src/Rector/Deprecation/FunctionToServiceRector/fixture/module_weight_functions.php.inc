<?php

module_set_weight('views', 10);
$sorted = module_config_sort($modules);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\Core\Extension\ModuleWeight::class)->set('views', 10), fn() => module_set_weight('views', 10));
$sorted = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal::service(\Drupal\Core\Extension\ModuleWeight::class)->sort($modules), fn() => module_config_sort($modules));
?>
