<?php

/**
 * Provides a form element.
 *
 * @FormElement("foo_eligible_product_for_current_user_select")
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>

-----
<?php

/**
 * Provides a form element.
 *
 * @FormElement("foo_eligible_product_for_current_user_select")
 */
#[\Drupal\Core\Render\Attribute\FormElement('foo_eligible_product_for_current_user_select')]
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
