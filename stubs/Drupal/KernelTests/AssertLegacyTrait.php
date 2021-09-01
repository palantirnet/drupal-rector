<?php

declare(strict_types=1);

namespace Drupal\KernelTests;

if (class_exists('Drupal\KernelTests\AssertLegacyTrait')) {
    return;
}

trait AssertLegacyTrait {

    protected function assert($actual, $message = '') {
        parent::assertTrue((bool) $actual, $message);
    }

    protected function assertEqual($actual, $expected, $message = '') {
        $this->assertEquals($expected, $actual, (string) $message);
    }

    protected function assertNotEqual($actual, $expected, $message = '') {
        $this->assertNotEquals($expected, $actual, (string) $message);
    }

    protected function assertIdentical($actual, $expected, $message = '') {
        $this->assertSame($expected, $actual, (string) $message);
    }

    protected function assertNotIdentical($actual, $expected, $message = '') {
        $this->assertNotSame($expected, $actual, (string) $message);
    }

    protected function assertIdenticalObject($actual, $expected, $message = '') {
        $this->assertEquals($expected, $actual, (string) $message);
    }

    protected function pass($message) {
        $this->assertTrue(TRUE, $message);
    }

}
