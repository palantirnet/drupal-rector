<?php

function example($entity, $entity_type, $bundle, $form, $form_state, $element, $entity_type_id) {
    content_translation_translate_access($entity);
    content_translation_enable_widget($entity_type, $bundle, $form, $form_state);
    content_translation_language_configuration_element_process($element, $form_state, $form);
    content_translation_language_configuration_element_validate($element, $form_state, $form);
    content_translation_language_configuration_element_submit($form, $form_state);
    _content_translation_install_field_storage_definitions($entity_type_id);
}
?>
-----
<?php

function example($entity, $entity_type, $bundle, $form, $form_state, $element, $entity_type_id) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('content_translation.manager')->access($entity), fn() => content_translation_translate_access($entity));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->getWidget($entity_type, $bundle, $form, $form_state), fn() => content_translation_enable_widget($entity_type, $bundle, $form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->configElementProcess($element, $form_state, $form), fn() => content_translation_language_configuration_element_process($element, $form_state, $form));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->configElementValidate($element, $form_state, $form), fn() => content_translation_language_configuration_element_validate($element, $form_state, $form));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->configElementSubmit($form, $form_state), fn() => content_translation_language_configuration_element_submit($form, $form_state));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\content_translation\Hook\ContentTranslationHooks')->installFieldStorageDefinitions($entity_type_id), fn() => _content_translation_install_field_storage_definitions($entity_type_id));
}
?>
