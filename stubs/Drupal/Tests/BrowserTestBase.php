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

    /**
     * The theme to install as the default for testing.
     *
     * Defaults to the install profile's default theme, if it specifies any.
     *
     * @var string
     */
    protected $defaultTheme;

}
