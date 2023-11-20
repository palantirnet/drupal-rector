<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\AssertLegacyTraitRector;
use DrupalRector\Drupal9\Rector\ValueObject\AssertLegacyTraitConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertLegacyTraitRector::class, $rectorConfig, true, [
        new AssertLegacyTraitConfiguration('assertLinkByHref', 'linkByHrefExists'),
        new AssertLegacyTraitConfiguration('assertLink', 'linkExists'),
        new AssertLegacyTraitConfiguration('assertNoEscaped', 'assertNoEscaped'),
        new AssertLegacyTraitConfiguration('assertNoFieldChecked', 'checkboxNotChecked'),
        new AssertLegacyTraitConfiguration('assertNoLinkByHref', 'linkByHrefNotExists'),
        new AssertLegacyTraitConfiguration('assertNoLink', 'linkNotExists'),
        new AssertLegacyTraitConfiguration('assertNoOption', 'optionNotExists'),
        new AssertLegacyTraitConfiguration('assertNoPattern', 'responseNotMatches'),
        new AssertLegacyTraitConfiguration('assertPattern', 'responseMatches'),
        new AssertLegacyTraitConfiguration('assertElementNotPresent', 'elementNotExists'),
        new AssertLegacyTraitConfiguration('assertElementPresent', 'elementExists'),
        new AssertLegacyTraitConfiguration('assertFieldChecked', 'checkboxChecked'),
        new AssertLegacyTraitConfiguration('assertHeader', 'responseHeaderEquals'),
        new AssertLegacyTraitConfiguration('assertOptionByText', 'optionExists'),
        new AssertLegacyTraitConfiguration('assertOption', 'optionExists'),
        new AssertLegacyTraitConfiguration('assertResponse', 'statusCodeEquals'),
        new AssertLegacyTraitConfiguration('assertTitle', 'titleEquals'),
        new AssertLegacyTraitConfiguration('assertUniqueText', 'pageTextContainsOnce'),
        new AssertLegacyTraitConfiguration('assertUrl', 'addressEquals'),
        new AssertLegacyTraitConfiguration('buildXPathQuery', 'buildXPathQuery'),
        new AssertLegacyTraitConfiguration('assertEscaped', 'assertEscaped'),
        new AssertLegacyTraitConfiguration('assertNoEscaped', 'assertNoEscaped'),

        new AssertLegacyTraitConfiguration('assertField', 'fieldExists', 'Change assertion to buttonExists() if checking for a button.'),
        new AssertLegacyTraitConfiguration('assertNoField', 'fieldNotExists', 'Change assertion to buttonExists() if checking for a button.'),

        new AssertLegacyTraitConfiguration('assertNoRaw', 'responseNotContains', '', true, true),
        new AssertLegacyTraitConfiguration('assertRaw', 'responseContains', '', true, true),

        new AssertLegacyTraitConfiguration('assertNoText', 'pageTextNotContains', 'Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.'.PHP_EOL.'// The passed text should be HTML decoded, exactly as a human sees it in the browser.', true, true),
        new AssertLegacyTraitConfiguration('assertText', 'pageTextContains', 'Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.'.PHP_EOL.'// The passed text should be HTML decoded, exactly as a human sees it in the browser.', true, true),

        new AssertLegacyTraitConfiguration('assertEqual', 'assertEquals', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertNotEqual', 'assertNotEquals', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertIdenticalObject', 'assertEquals', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertIdentical', 'assertSame', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertNotIdentical', 'assertNotSame', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assert', 'assertTrue', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),

        new AssertLegacyTraitConfiguration('assertNoCacheTag', 'responseHeaderNotContains', '', true, false, 'Drupal\FunctionalTests\AssertLegacyTrait', 'X-Drupal-Cache-Tags'),
        new AssertLegacyTraitConfiguration('assertCacheTag', 'responseHeaderContains', '', true, false, 'Drupal\FunctionalTests\AssertLegacyTrait', 'X-Drupal-Cache-Tags'),
    ]);
};
