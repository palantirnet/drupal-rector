<?php

/**
 * Provides a block to display the page title.
 *
 * @Block(
 *   id = "page_title_block",
 *   admin_label = @Translation("Page title"),
 *   forms = {
 *     "settings_tray" = FALSE,
 *   },
 * )
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>

-----
<?php

/**
 * Provides a block to display the page title.
 *
 * @Block(
 *   id = "page_title_block",
 *   admin_label = @Translation("Page title"),
 *   forms = {
 *     "settings_tray" = FALSE,
 *   },
 * )
 */
#[\Drupal\Core\Action\Attribute\Block(id: 'page_title_block', admin_label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Page title'), forms: ['settings_tray' => false])]
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

}
?>
