<?php

function simple_example() {
    $metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
    $metadata_bag->clearCsrfTokenSeed();

    /** @var \Drupal\Core\Entity\EntityInterface $untranslated_entity */
    $untranslated_entity = \Drupal::entityTypeManager()->getStorage('node')->load(123);
    $form_state->setRedirectUrl($untranslated_entity->urlInfo('canonical'));

    $toolkit = new \Drupal\system\Plugin\ImageToolkit\GDToolkit;
    $toolkit->getResource();
    $toolkit->setResource();
}
?>
-----
<?php

function simple_example() {
    $metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
    $metadata_bag->stampNew();

    /** @var \Drupal\Core\Entity\EntityInterface $untranslated_entity */
    $untranslated_entity = \Drupal::entityTypeManager()->getStorage('node')->load(123);
    $form_state->setRedirectUrl($untranslated_entity->toUrl('canonical'));

    $toolkit = new \Drupal\system\Plugin\ImageToolkit\GDToolkit;
    $toolkit->getImage();
    $toolkit->setImage();
}
?>
