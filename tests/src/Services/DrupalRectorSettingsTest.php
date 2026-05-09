<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Services;

use DrupalRector\Services\DrupalRectorSettings;
use PHPUnit\Framework\TestCase;

final class DrupalRectorSettingsTest extends TestCase
{
    public function testDefaults(): void
    {
        $settings = new DrupalRectorSettings();

        self::assertTrue($settings->isBackwardCompatibilityEnabled());
        self::assertSame('10.1.0', $settings->getMinimumCoreVersionSupported());
        self::assertNull($settings->getDrupalVersion());
    }

    public function testDisableBackwardCompatibilityReturnsSelf(): void
    {
        $settings = new DrupalRectorSettings();
        $result = $settings->disableBackwardCompatibility();

        self::assertSame($settings, $result);
        self::assertFalse($settings->isBackwardCompatibilityEnabled());
    }

    public function testEnableBackwardCompatibilityAfterDisable(): void
    {
        $settings = new DrupalRectorSettings();
        $settings->disableBackwardCompatibility();
        $settings->enableBackwardCompatibility();

        self::assertTrue($settings->isBackwardCompatibilityEnabled());
    }

    public function testEnableBackwardCompatibilityReturnsSelf(): void
    {
        $settings = new DrupalRectorSettings();
        $result = $settings->enableBackwardCompatibility();

        self::assertSame($settings, $result);
    }

    public function testSetMinimumCoreVersionSupported(): void
    {
        $settings = new DrupalRectorSettings();
        $result = $settings->setMinimumCoreVersionSupported('11.0.0');

        self::assertSame($settings, $result);
        self::assertSame('11.0.0', $settings->getMinimumCoreVersionSupported());
    }

    public function testSetMinimumCoreVersionSupportedThrowsOnEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DrupalRectorSettings())->setMinimumCoreVersionSupported('');
    }

    public function testSetDrupalVersion(): void
    {
        $settings = new DrupalRectorSettings();
        $result = $settings->setDrupalVersion('99.99.99');

        self::assertSame($settings, $result);
        self::assertSame('99.99.99', $settings->getDrupalVersion());
    }

    public function testSetDrupalVersionCanBeResetToNull(): void
    {
        $settings = new DrupalRectorSettings();
        $settings->setDrupalVersion('11.0.0');
        $settings->setDrupalVersion(null);

        self::assertNull($settings->getDrupalVersion());
    }

    public function testFluentChain(): void
    {
        $settings = (new DrupalRectorSettings())
            ->disableBackwardCompatibility()
            ->setMinimumCoreVersionSupported('10.5.0')
            ->setDrupalVersion('10.5.1');

        self::assertFalse($settings->isBackwardCompatibilityEnabled());
        self::assertSame('10.5.0', $settings->getMinimumCoreVersionSupported());
        self::assertSame('10.5.1', $settings->getDrupalVersion());
    }
}
