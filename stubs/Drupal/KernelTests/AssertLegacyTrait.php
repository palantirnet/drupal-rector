<?php

declare(strict_types=1);

namespace Drupal\KernelTests;

if (class_exists('Drupal\KernelTests\AssertLegacyTrait')) {
    return;
}

trait AssertLegacyTrait {

    protected function pass($message) {
        $this->assertTrue(TRUE, $message);
    }

}
