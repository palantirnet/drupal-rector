<?php

declare(strict_types=1);

namespace Drupal\Tests;

use Drupal\FunctionalTests\AssertLegacyTrait;
use PHPUnit\Framework\TestCase;

if (class_exists('Drupal\Tests\UnitTestCase')) {
    return;
}

abstract class UnitTestCase extends TestCase
{

}
