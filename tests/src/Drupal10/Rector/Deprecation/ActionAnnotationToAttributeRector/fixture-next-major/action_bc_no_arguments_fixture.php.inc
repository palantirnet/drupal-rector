<?php

/**
 * An action annotation without arguments.
 *
 * @Action
 */
class NoArgumentsExample extends ActionBase {

}
?>
-----
<?php

/**
 * An action annotation without arguments.
 */
#[\Drupal\Core\Action\Attribute\Action]
class NoArgumentsExample extends ActionBase {

}
?>
