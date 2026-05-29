<?php

function example($node, $values, $form, $form_state) {
    _menu_ui_node_save($node, $values);
    $defaults = menu_ui_get_menu_link_defaults($node);
    menu_ui_node_builder($node, $form, $form_state);
    menu_ui_form_node_form_submit($form, $form_state);
    menu_ui_form_node_type_form_validate($form, $form_state);
    menu_ui_form_node_type_form_builder($node, $form, $form_state);
}
?>
-----
<?php

function example($node, $values, $form, $form_state) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\MenuUiUtility')->menuUiNodeSave($node, $values), fn() => _menu_ui_node_save($node, $values));
    $defaults = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\MenuUiUtility')->getMenuLinkDefaults($node), fn() => menu_ui_get_menu_link_defaults($node));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\Hook\MenuUiHooks')->nodeBuilder($node, $form, $form_state), fn() => menu_ui_node_builder($node, $form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\Hook\MenuUiHooks')->formNodeFormSubmit($form, $form_state), fn() => menu_ui_form_node_form_submit($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\Hook\MenuUiHooks')->formNodeTypeFormValidate($form, $form_state), fn() => menu_ui_form_node_type_form_validate($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\menu_ui\Hook\MenuUiHooks')->formNodeTypeFormBuilder($node, $form, $form_state), fn() => menu_ui_form_node_type_form_builder($node, $form, $form_state));
}
?>
