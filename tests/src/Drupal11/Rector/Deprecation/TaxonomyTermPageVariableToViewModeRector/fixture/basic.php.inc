<?php

function mymodule_preprocess_taxonomy_term(array &$variables): void {
  if ($variables['page']) {
    $variables['show_title'] = FALSE;
  }
}
?>
-----
<?php

function mymodule_preprocess_taxonomy_term(array &$variables): void {
  if ($variables['view_mode'] === 'full') {
    $variables['show_title'] = FALSE;
  }
}
?>
