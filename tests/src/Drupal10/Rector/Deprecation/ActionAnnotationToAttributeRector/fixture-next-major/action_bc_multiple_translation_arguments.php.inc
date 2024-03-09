<?php

/**
 * A basic example action that does nothing.
 *
 * @Action(
 *   id = "action_example_basic_action",
 *   action_label = @Translation("Action Example: A basic example action that does nothing and has an @argument", context = "Validation", arguments = {"@argument" = "Argument"}),
 *   type = "system"
 * )
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>

-----
<?php

/**
 * A basic example action that does nothing.
 */
#[\Drupal\Core\Action\Attribute\Action(id: 'action_example_basic_action', action_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Action Example: A basic example action that does nothing and has an @argument', ['@argument' => 'Argument'], ['context' => 'Validation']), type: 'system')]
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
