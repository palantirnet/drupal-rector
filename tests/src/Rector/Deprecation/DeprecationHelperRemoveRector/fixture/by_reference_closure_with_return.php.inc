<?php

function example($entity_type, $bundle, &$form, $form_state)
{
    $form = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', function () use (&$form) {
        return \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->getWidget($entity_type, $bundle, $form, $form_state);
    }, function () use (&$form) {
        return content_translation_enable_widget($entity_type, $bundle, $form, $form_state);
    });
}

?>
-----
<?php

function example($entity_type, $bundle, &$form, $form_state)
{
    $form = \Drupal::service('Drupal\content_translation\ContentTranslationEnableTranslationPerBundle')->getWidget($entity_type, $bundle, $form, $form_state);
}

?>
