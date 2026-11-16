<?php

namespace Drupal\my_module;

use Drupal\Core\Controller\ControllerBase;

class SessionStore extends ControllerBase {

  public function store($value): void {
    $_SESSION['my_module_value'] = $value;
  }

  public function load() {
    return isset($_SESSION['my_module_value']) ? $_SESSION['my_module_value'] : NULL;
  }

}

function my_module_helper() {
  return $_SESSION['my_module_value'];
}

?>
-----
<?php

namespace Drupal\my_module;

use Drupal\Core\Controller\ControllerBase;

class SessionStore extends ControllerBase {

  public function store($value): void {
    \Drupal::request()->getSession()->set('my_module_value', $value);
  }

  public function load() {
    return \Drupal::request()->getSession()->get('my_module_value') !== NULL ? \Drupal::request()->getSession()->get('my_module_value') : NULL;
  }

}

function my_module_helper() {
  return \Drupal::request()->getSession()->get('my_module_value');
}

?>
