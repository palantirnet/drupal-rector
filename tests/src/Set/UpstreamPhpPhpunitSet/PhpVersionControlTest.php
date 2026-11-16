<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set\UpstreamPhpPhpunitSet;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\ValueObject\PhpVersion;

/**
 * Shows why the set raises the target PHP version: without it, a project that
 * targets PHP 8.1 never gets ExplicitNullableParamTypeRector.
 */
final class PhpVersionControlTest extends AbstractDrupalRectorTestCase
{
    /**
     * Set once for the class: Rector loads the config on the first test only,
     * and the version has to be in place by then, not reset before every test.
     */
    public static function setUpBeforeClass(): void
    {
        SimpleParameterProvider::setParameter(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_81);

        parent::setUpBeforeClass();
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__.'/fixture-not-loaded/nullable_rule_skipped_on_php_81.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/nullable_rule_only.php';
    }
}
