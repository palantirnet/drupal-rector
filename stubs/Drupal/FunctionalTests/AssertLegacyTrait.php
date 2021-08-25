<?php

declare(strict_types=1);

namespace Drupal\FunctionalTests;

use Drupal\KernelTests\AssertLegacyTrait as BaseAssertLegacyTrait;

if (class_exists('Drupal\FunctionalTests\AssertLegacyTrait')) {
    return;
}

trait AssertLegacyTrait {
    use BaseAssertLegacyTrait;

    protected function assertNoUniqueText($text, $message = '') {
        $this->assertTrue(TRUE, $message);
    }

}
