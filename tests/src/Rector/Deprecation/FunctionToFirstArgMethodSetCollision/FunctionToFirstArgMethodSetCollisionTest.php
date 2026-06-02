<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation\FunctionToFirstArgMethodSetCollision;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

/**
 * Regression test for the Drupal 9 + Drupal 11 set collision.
 *
 * The Drupal 9 FunctionToFirstArgMethodRector subclasses the generic rule, so
 * Rector's container fires the generic rule's configuration callback on the
 * subclass instance too (afterResolving callbacks match by instanceof). When
 * both sets are loaded this delivered the generic, version-tagged configuration
 * to the subclass, whose strict type guard threw at container-build time. The
 * subclass now ignores configuration that is not its own.
 */
class FunctionToFirstArgMethodSetCollisionTest extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function test(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
