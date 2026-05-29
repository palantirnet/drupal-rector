<?php

function example() {
    file_managed_file_submit($form, $form_state);
}
?>
-----
<?php

function example() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\file\Element\ManagedFile::submit($form, $form_state), fn() => file_managed_file_submit($form, $form_state));
}
?>
