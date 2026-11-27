<?php

namespace Drupal\Tests\my_module\Unit;

use PHPUnit\Framework\MockObject\Stub\ReturnValueMap;
use PHPUnit\Framework\TestCase;

class WillMethodTest extends TestCase {

  public function testStubs(): void {
    $mock = $this->createMock(\Countable::class);
    $mock->method('count')->will($this->returnValue(3));
    $mock->method('count')->will($this->returnSelf());
    $mock->method('count')->will($this->returnArgument(0));
    $mock->method('count')->will($this->returnCallback(fn () => 1));
    $mock->method('count')->will($this->throwException(new \Exception()));
    $mock->method('count')->will($this->onConsecutiveCalls(1, 2));
    $mock->method('count')->will(new ReturnValueMap([['a', 'b']]));
    $mock->method('count')->willReturn(4);
  }

}

?>
-----
<?php

namespace Drupal\Tests\my_module\Unit;

use PHPUnit\Framework\MockObject\Stub\ReturnValueMap;
use PHPUnit\Framework\TestCase;

class WillMethodTest extends TestCase {

  public function testStubs(): void {
    $mock = $this->createMock(\Countable::class);
    $mock->method('count')->willReturn(3);
    $mock->method('count')->willReturnSelf();
    $mock->method('count')->willReturnArgument(0);
    $mock->method('count')->willReturnCallback(fn () => 1);
    $mock->method('count')->willThrowException(new \Exception());
    $mock->method('count')->willReturnOnConsecutiveCalls(1, 2);
    $mock->method('count')->willReturnMap([['a', 'b']]);
    $mock->method('count')->willReturn(4);
  }

}

?>
