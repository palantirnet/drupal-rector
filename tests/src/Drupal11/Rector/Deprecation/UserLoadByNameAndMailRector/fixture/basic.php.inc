<?php

$account = user_load_by_name($name);
$account_by_mail = user_load_by_mail($mail);
?>
-----
<?php

$account = array_values(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $name]))[0] ?? FALSE;
$account_by_mail = array_values(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $mail]))[0] ?? FALSE;
?>
