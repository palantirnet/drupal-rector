<?php

declare(strict_types=1);

namespace Rector\Tests\DrupalRector\Rector\ConstFetch\SymfonyCmfRouteObjectInterfaceConstantsRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class SymfonyCmfRouteObjectInterfaceConstantsRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(\Symplify\SmartFileSystem\SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return \Iterator<\Symplify\SmartFileSystem\SmartFileInfo>
     */
    public function provideData(): \Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
