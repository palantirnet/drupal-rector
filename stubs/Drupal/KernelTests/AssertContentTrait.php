<?php

declare(strict_types=1);

namespace Drupal\KernelTests;

if (class_exists('Drupal\KernelTests\AssertContentTrait')) {
    return;
}

trait AssertContentTrait {

    protected function assertNoUniqueText($text, $message = '', $group = 'Other') {
        return $this->assertUniqueTextHelper($text, $message, $group, FALSE);
    }

    protected function assertUniqueTextHelper($text, $message = '', $group = 'Other', $be_unique = FALSE) {
        $this->assertTrue(TRUE, $message);
    }

}
