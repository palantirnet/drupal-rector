<?php

function mymodule_preprocess_node(array &$variables): void {
  if ($variables['page']) {
    $variables['show_title'] = FALSE;
  }
}
