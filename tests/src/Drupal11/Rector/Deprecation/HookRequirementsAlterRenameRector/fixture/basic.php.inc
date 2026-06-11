<?php

function mymodule_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}

?>
-----
<?php

function mymodule_runtime_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}

?>
