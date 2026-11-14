<?php

/**
 * A basic example action with a translation context but no arguments.
 *
 * @Action(
 *   id = "action_example_context_action",
 *   action_label = @Translation("Source or file required", context = "Validation"),
 *   type = "system"
 * )
 */
class ContextExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
-----
<?php

/**
 * A basic example action with a translation context but no arguments.
 *
 * @Action(
 *   id = "action_example_context_action",
 *   action_label = @Translation("Source or file required", context = "Validation"),
 *   type = "system"
 * )
 */
#[\Drupal\Core\Action\Attribute\Action(id: 'action_example_context_action', action_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Source or file required', [], ['context' => 'Validation']), type: 'system')]
class ContextExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
