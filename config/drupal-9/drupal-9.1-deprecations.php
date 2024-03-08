<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByIdRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByIdRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertOptionSelectedRector;
use DrupalRector\Drupal9\Rector\Deprecation\ConstructFieldXpathRector;
use DrupalRector\Drupal9\Rector\Deprecation\GetAllOptionsRector;
use DrupalRector\Drupal9\Rector\Deprecation\GetRawContentRector;
use DrupalRector\Drupal9\Rector\Deprecation\PassRector;
use DrupalRector\Drupal9\Rector\Deprecation\UiHelperTraitDrupalPostFormRector;
use DrupalRector\Drupal9\Rector\Deprecation\UserPasswordRector;
use DrupalRector\Drupal9\Rector\ValueObject\AssertLegacyTraitConfiguration;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\ValueObject\RenameStaticMethod;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });

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
    $rectorConfig->rule(AssertNoFieldByNameRector::class);
    $rectorConfig->rule(AssertFieldByIdRector::class);

    $rectorConfig->ruleWithConfiguration(DrupalRector\Drupal9\Rector\Deprecation\AssertLegacyTraitRector::class, [
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

    $rectorConfig->rule(AssertNoFieldByIdRector::class);
    $rectorConfig->rule(AssertOptionSelectedRector::class);

    // @todo AssertFieldByXPathRector
    // @todo AssertNoFieldByXPathRector
    // @todo AssertFieldsByValueRector

    $rectorConfig->rule(ConstructFieldXpathRector::class);
    $rectorConfig->rule(GetRawContentRector::class);
    $rectorConfig->rule(GetAllOptionsRector::class);
    $rectorConfig->rule(UserPasswordRector::class);

    // Change record: https://www.drupal.org/node/3162663
    $rectorConfig->ruleWithConfiguration(RenameStaticMethodRector::class, [
        new RenameStaticMethod(
            'Drupal\Component\Utility\Bytes',
            'toInt',
            'Drupal\Component\Utility\Bytes',
            'toNumber'
        ),
    ]);

    // Change record: https://www.drupal.org/node/3151009 (only constants are supported)
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_NAME',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
        ),
    ]);
};
