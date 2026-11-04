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
 *
 * @Action
 */
#[\Drupal\Core\Action\Attribute\Action]
class NoArgumentsExample extends ActionBase {

}
?>
