<?php

declare(strict_types=1);

namespace Drupal\KernelTests;

use PHPUnit\Framework\TestCase;

if (class_exists('Drupal\KernelTests\KernelTestBase')) {
    return;
}

abstract class KernelTestBase extends TestCase {

    use AssertLegacyTrait;
    use AssertContentTrait;

}
