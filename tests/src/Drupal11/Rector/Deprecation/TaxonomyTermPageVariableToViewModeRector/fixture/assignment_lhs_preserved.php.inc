<?php

function mymodule_preprocess_taxonomy_term(array &$variables): void {
  $variables['page'] = TRUE;
  if ($variables['page']) {
    $variables['show_title'] = FALSE;
  }
}
?>
-----
<?php

function mymodule_preprocess_taxonomy_term(array &$variables): void {
  $variables['page'] = TRUE;
  if ($variables['view_mode'] === 'full') {
    $variables['show_title'] = FALSE;
  }
}
?>
