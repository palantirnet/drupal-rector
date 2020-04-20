<?php

namespace DrupalRector\Tests\DrupalUrlRectorTest;

use DrupalRector\Rector\Deprecation\DrupalURLRector;
use Iterator;
use Rector\Core\Testing\PHPUnit\AbstractRectorTestCase;

final class DrupalUrlRectorTest extends AbstractRectorTestCase
{
  protected function getRectorClass(): string
  {
    return DrupalURLRector::class;
  }

  /**
   * @dataProvider provideData()
   */
  public function test(string $file): void
  {
    $this->doTestFile($file);
  }

  public function provideData(): Iterator
  {
    return $this->yieldFilesFromDirectory(__DIR__ . '/Fixtures');
  }
}