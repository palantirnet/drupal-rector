<?php

use Drupal\views\Views;

// handlerManager with a dynamic argument uses the service-locator form.
$type = 'relationship';
$manager = Views::handlerManager($type);

?>
-----
<?php

use Drupal\views\Views;

// handlerManager with a dynamic argument uses the service-locator form.
$type = 'relationship';
$manager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('views.plugin_managers')->get($type), fn() => Views::handlerManager($type));

?>
