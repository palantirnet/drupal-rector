<?php

/**
 * Provides a form element with a positional id and keyed values.
 *
 * @FormElement("my_element",
 *   label = @Translation("My element"),
 *   type = "system"
 * )
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>

-----
<?php

/**
 * Provides a form element with a positional id and keyed values.
 *
 * @FormElement("my_element",
 *   label = @Translation("My element"),
 *   type = "system"
 * )
 */
#[\Drupal\Core\Render\Attribute\FormElement('my_element', label: new \Drupal\Core\StringTranslation\TranslatableMarkup('My element'), type: 'system')]
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
