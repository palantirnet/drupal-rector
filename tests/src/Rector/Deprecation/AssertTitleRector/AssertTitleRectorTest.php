<?php declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation\AssertTitleRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

class AssertTitleRectorTest extends AbstractRectorTestCase {

    /**
     * @covers ::refactor
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<<string>>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/fixture');
    }

    public function provideConfigFilePath(): string
    {
        // must be implemented
        return __DIR__ . '/config/configured_rule.php';
    }

}
