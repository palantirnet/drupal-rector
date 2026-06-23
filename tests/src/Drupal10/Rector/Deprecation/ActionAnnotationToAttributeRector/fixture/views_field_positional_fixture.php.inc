<?php

/**
 * Provides a views field handler.
 *
 * @ViewsField("field_id")
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>

-----
<?php

/**
 * Provides a views field handler.
 *
 * @ViewsField("field_id")
 */
#[\Drupal\views\Attribute\ViewsField('field_id')]
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
