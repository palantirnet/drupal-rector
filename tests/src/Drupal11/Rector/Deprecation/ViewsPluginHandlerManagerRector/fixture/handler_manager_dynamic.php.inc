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
$manager = \Drupal::service('views.plugin_managers')->get($type);

?>
