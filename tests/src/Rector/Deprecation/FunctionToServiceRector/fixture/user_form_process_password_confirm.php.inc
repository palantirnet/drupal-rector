<?php

function example($element) {
    return user_form_process_password_confirm($element);
}
?>
-----
<?php

function example($element) {
    return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\user\Hook\UserThemeHooks')->processPasswordConfirm($element), fn() => user_form_process_password_confirm($element));
}
?>
