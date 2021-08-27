<?php

declare(strict_types=1);

namespace Drupal\Tests;

use Drupal\FunctionalTests\AssertLegacyTrait;
use PHPUnit\Framework\TestCase;

if (class_exists('Drupal\Tests\BrowserTestBase')) {
    return;
}

abstract class BrowserTestBase extends TestCase
{
    use AssertLegacyTrait;

}
