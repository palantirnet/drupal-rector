<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertElementNotPresentRector;
use DrupalRector\Rector\Deprecation\AssertElementPresentRector;
use DrupalRector\Rector\Deprecation\AssertEqualRector;
use DrupalRector\Rector\Deprecation\AssertEscapedRector;
use DrupalRector\Rector\Deprecation\AssertFieldByIdRector;
use DrupalRector\Rector\Deprecation\AssertFieldByNameRector;
use DrupalRector\Rector\Deprecation\AssertFieldCheckedRector;
use DrupalRector\Rector\Deprecation\AssertFieldRector;
use DrupalRector\Rector\Deprecation\AssertHeaderRector;
use DrupalRector\Rector\Deprecation\AssertIdenticalObjectRector;
use DrupalRector\Rector\Deprecation\AssertIdenticalRector;
use DrupalRector\Rector\Deprecation\AssertLinkByHrefRector;
use DrupalRector\Rector\Deprecation\AssertLinkRector;
use DrupalRector\Rector\Deprecation\AssertNoCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertNoEscapedRector;
use DrupalRector\Rector\Deprecation\AssertNoFieldByIdRector;
use DrupalRector\Rector\Deprecation\AssertNoFieldByNameRector;
use DrupalRector\Rector\Deprecation\AssertNoFieldCheckedRector;
use DrupalRector\Rector\Deprecation\AssertNoFieldRector;
use DrupalRector\Rector\Deprecation\AssertNoLinkByHrefRector;
use DrupalRector\Rector\Deprecation\AssertNoLinkRector;
use DrupalRector\Rector\Deprecation\AssertNoOptionRector;
use DrupalRector\Rector\Deprecation\AssertNoPatternRector;
use DrupalRector\Rector\Deprecation\AssertNoRawRector;
use DrupalRector\Rector\Deprecation\AssertNotEqualRector;
use DrupalRector\Rector\Deprecation\AssertNoTextRector;
use DrupalRector\Rector\Deprecation\AssertNotIdenticalRector;
use DrupalRector\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Rector\Deprecation\AssertOptionByTextRector;
use DrupalRector\Rector\Deprecation\AssertOptionRector;
use DrupalRector\Rector\Deprecation\AssertOptionSelectedRector;
use DrupalRector\Rector\Deprecation\AssertPatternRector;
use DrupalRector\Rector\Deprecation\AssertRawRector;
use DrupalRector\Rector\Deprecation\AssertRector;
use DrupalRector\Rector\Deprecation\AssertResponseRector;
use DrupalRector\Rector\Deprecation\AssertTextRector;
use DrupalRector\Rector\Deprecation\AssertTitleRector;
use DrupalRector\Rector\Deprecation\AssertUniqueTextRector;
use DrupalRector\Rector\Deprecation\AssertUrlRector;
use DrupalRector\Rector\Deprecation\BuildXPathQueryRector;
use DrupalRector\Rector\Deprecation\ConstructFieldXpathRector;
use DrupalRector\Rector\Deprecation\GetAllOptionsRector;
use DrupalRector\Rector\Deprecation\GetRawContentRector;
use DrupalRector\Rector\Deprecation\PassRector;
use DrupalRector\Rector\Deprecation\UiHelperTraitDrupalPostFormRector;
use DrupalRector\Rector\Deprecation\UserPasswordRector;
use DrupalRector\Rector\ValueObject\AssertLegacyTraitConfiguration;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90,
    ]);

    $rectorConfig->rule(UiHelperTraitDrupalPostFormRector::class);
    // AssertLegacyTrait items
    // @see https://www.drupal.org/project/rector/issues/3222671
    // @see https://www.drupal.org/node/3129738

    $rectorConfig->rule(PassRector::class);

    $rectorConfig->rule(AssertNoUniqueTextRector::class);
    $rectorConfig->rule(AssertFieldByNameRector::class);
    $rectorConfig->ruleWithConfiguration(AssertNoFieldByNameRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertFieldByIdRector::class);


    $rectorConfig->ruleWithConfiguration(\DrupalRector\Rector\Deprecation\AssertLegacyTraitRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
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

        new AssertLegacyTraitConfiguration( 'assertNoText',  'pageTextNotContains', 'Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.' . PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.', true, true),
        new AssertLegacyTraitConfiguration('assertText', 'pageTextContains', 'Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.' . PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.', true, true),

        new AssertLegacyTraitConfiguration('assertEqual', 'assertEquals', '',  false, false,  'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertNotEqual', 'assertNotEquals', '',  false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertIdenticalObject', 'assertEquals', '',  false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertIdentical', 'assertSame', '',  false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assertNotIdentical', 'assertNotSame', '',  false, false, 'Drupal\KernelTests\AssertLegacyTrait'),
        new AssertLegacyTraitConfiguration('assert', 'assertTrue', '', false, false, 'Drupal\KernelTests\AssertLegacyTrait'),

        new AssertLegacyTraitConfiguration('assertNoCacheTag', 'responseHeaderNotContains', '', true, false, 'Drupal\FunctionalTests\AssertLegacyTrait', 'X-Drupal-Cache-Tags'),
        new AssertLegacyTraitConfiguration('assertCacheTag', 'responseHeaderContains', '', true, false, 'Drupal\FunctionalTests\AssertLegacyTrait', 'X-Drupal-Cache-Tags'),
    ]);

    $rectorConfig->rule(AssertNoFieldByIdRector::class);
    $rectorConfig->rule(AssertOptionSelectedRector::class);


    // @todo AssertFieldByXPathRector
    // @todo AssertNoFieldByXPathRector
    // @todo AssertFieldsByValueRector

    $rectorConfig->rule(ConstructFieldXpathRector::class);
    $rectorConfig->rule(GetRawContentRector::class);
    $rectorConfig->rule(GetAllOptionsRector::class);
    $rectorConfig->rule(UserPasswordRector::class);
};
