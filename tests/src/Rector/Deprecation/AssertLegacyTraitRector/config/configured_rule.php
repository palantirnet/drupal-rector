<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertLegacyTraitRector;
use DrupalRector\Rector\ValueObject\AssertLegacyTraitConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AssertLegacyTraitRector::class, $rectorConfig, TRUE, [
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertLinkByHref', methodName: 'linkByHrefExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertLink', methodName: 'linkExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoEscaped', methodName: 'assertNoEscaped'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoFieldChecked', methodName: 'checkboxNotChecked'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoLinkByHref', methodName: 'linkByHrefNotExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoLink', methodName: 'linkNotExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoOption', methodName: 'optionNotExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoPattern', methodName: 'responseNotMatches'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertPattern', methodName: 'responseMatches'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertElementNotPresent', methodName: 'elementNotExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertElementPresent', methodName: 'elementExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertFieldChecked', methodName: 'checkboxChecked'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertHeader', methodName: 'responseHeaderEquals'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertOptionByText', methodName: 'optionExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertOption', methodName: 'optionExists'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertResponse', methodName: 'statusCodeEquals'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertTitle', methodName: 'titleEquals'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertUniqueText', methodName: 'pageTextContainsOnce'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertUrl', methodName: 'addressEquals'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'buildXPathQuery', methodName: 'buildXPathQuery'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertEscaped', methodName: 'assertEscaped'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoEscaped', methodName: 'assertNoEscaped'),

        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertField', methodName: 'fieldExists', comment: 'Change assertion to buttonExists() if checking for a button.'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoField', methodName: 'fieldNotExists', comment: 'Change assertion to buttonExists() if checking for a button.'),

        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoRaw', methodName: 'responseNotContains', processFirstArgumentOnly: true),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertRaw', methodName: 'responseContains', processFirstArgumentOnly: true),

        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoText', methodName: 'pageTextNotContains', comment: 'Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.' . PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.', processFirstArgumentOnly: true),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertText', methodName: 'pageTextContains', comment: 'Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.' . PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.', processFirstArgumentOnly: true),

        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertEqual', methodName: 'assertEquals', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNotEqual', methodName: 'assertNotEquals', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertIdenticalObject', methodName: 'assertEquals', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertIdentical', methodName: 'assertSame', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNotIdentical', methodName: 'assertNotSame', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assert', methodName: 'assertTrue', isAssertSessionMethod: false, declaringSource: 'Drupal\KernelTests\AssertLegacyTrait'),

        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertNoCacheTag', methodName: 'responseHeaderNotContains', prependArgument: 'X-Drupal-Cache-Tags'),
        new AssertLegacyTraitConfiguration(deprecatedMethodName: 'assertCacheTag', methodName: 'responseHeaderContains', prependArgument: 'X-Drupal-Cache-Tags'),
    ]);
};
