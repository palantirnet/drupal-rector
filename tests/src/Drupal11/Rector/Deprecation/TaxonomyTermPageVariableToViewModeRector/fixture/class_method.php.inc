<?php

namespace Drupal\mymodule\Hook;

class MyHooks {
  public function preprocessTaxonomyTerm(array &$variables): void {
    if ($variables['page']) {
      $variables['show_title'] = FALSE;
    }
  }
}
?>
-----
<?php

namespace Drupal\mymodule\Hook;

class MyHooks {
  public function preprocessTaxonomyTerm(array &$variables): void {
    if ($variables['view_mode'] === 'full') {
      $variables['show_title'] = FALSE;
    }
  }
}
?>
