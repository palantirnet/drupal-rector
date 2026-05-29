<?php

use Drupal\views\Views;

// Zero-arg calls are not transformed — the rector requires at least one argument.
Views::pluginManager();
Views::handlerManager();

?>
-----
<?php

use Drupal\views\Views;

// Zero-arg calls are not transformed — the rector requires at least one argument.
Views::pluginManager();
Views::handlerManager();

?>
