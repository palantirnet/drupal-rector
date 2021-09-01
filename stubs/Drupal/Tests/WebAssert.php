<?php

declare(strict_types=1);

namespace Drupal\Tests;

use Behat\Mink\WebAssert as MinkWebAssert;
use Drupal\FunctionalTests\AssertLegacyTrait;
use PHPUnit\Framework\TestCase;

if (class_exists('Drupal\Tests\WebAssert')) {
    return;
}

class WebAssert extends MinkWebAssert {

}
