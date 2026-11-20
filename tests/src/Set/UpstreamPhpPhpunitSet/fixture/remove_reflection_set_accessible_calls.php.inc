<?php

namespace Drupal\Tests\my_module\Unit;

class ReflectionHelper {

  public function readPrivate(object $object): mixed {
    $property = new \ReflectionProperty($object, 'secret');
    $property->setAccessible(TRUE);
    $method = new \ReflectionMethod($object, 'hidden');
    $method->setAccessible(TRUE);
    return $property->getValue($object) . $method->invoke($object);
  }

  public function notReflection(object $object): void {
    $object->setAccessible(TRUE);
  }

}

?>
-----
<?php

namespace Drupal\Tests\my_module\Unit;

class ReflectionHelper {

  public function readPrivate(object $object): mixed {
    $property = new \ReflectionProperty($object, 'secret');
    $method = new \ReflectionMethod($object, 'hidden');
    return $property->getValue($object) . $method->invoke($object);
  }

  public function notReflection(object $object): void {
    $object->setAccessible(TRUE);
  }

}

?>
