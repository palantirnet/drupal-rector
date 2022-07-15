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
    $rectorConfig->ruleWithConfiguration(AssertRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertEqualRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNotEqualRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertIdenticalRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNotIdenticalRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertIdenticalObjectRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(PassRector::class);
    $rectorConfig->ruleWithConfiguration(AssertElementPresentRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertElementNotPresentRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertTextRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoTextRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertUniqueTextRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertNoUniqueTextRector::class);
    $rectorConfig->ruleWithConfiguration(AssertResponseRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertFieldByNameRector::class);
    $rectorConfig->ruleWithConfiguration(AssertNoFieldByNameRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertFieldByIdRector::class);
    $rectorConfig->ruleWithConfiguration(AssertFieldRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoFieldRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertRawRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoRawRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertTitleRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertLinkRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoLinkRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertLinkByHrefRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoLinkByHrefRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertNoFieldByIdRector::class);
    $rectorConfig->ruleWithConfiguration(AssertUrlRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertOptionRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertOptionByTextRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoOptionRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(AssertOptionSelectedRector::class);
    $rectorConfig->ruleWithConfiguration(AssertFieldCheckedRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoFieldCheckedRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    // @todo AssertFieldByXPathRector
    // @todo AssertNoFieldByXPathRector
    // @todo AssertFieldsByValueRector
    $rectorConfig->ruleWithConfiguration(AssertEscapedRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoEscapedRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertPatternRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoPatternRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertCacheTagRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertNoCacheTagRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(AssertHeaderRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(BuildXPathQueryRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->rule(ConstructFieldXpathRector::class);
    $rectorConfig->rule(GetRawContentRector::class);
    $rectorConfig->rule(GetAllOptionsRector::class);
    $rectorConfig->rule(UserPasswordRector::class);
};
