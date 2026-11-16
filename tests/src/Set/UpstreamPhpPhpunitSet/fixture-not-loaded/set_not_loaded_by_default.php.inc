<?php

namespace Drupal\Tests\my_module\Unit;

use PHPUnit\Framework\TestCase;

class NotLoadedTest extends TestCase {

  public function build(\Countable $entity = NULL): void {
    $property = new \ReflectionProperty($this, 'secret');
    $property->setAccessible(TRUE);
    $flag = (boolean) $entity;
    $ch = curl_init();
    curl_close($ch);
    $this->createMock(\Countable::class)->method('count')->will($this->returnValue(3));
  }

}

?>
