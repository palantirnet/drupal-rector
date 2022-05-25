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

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $containerConfigurator->import(PHPUnitSetList::PHPUNIT_90);

    $services->set(UiHelperTraitDrupalPostFormRector::class);
    // AssertLegacyTrait items
    // @see https://www.drupal.org/project/rector/issues/3222671
    // @see https://www.drupal.org/node/3129738
    $services->set(AssertRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertEqualRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNotEqualRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertIdenticalRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNotIdenticalRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertIdenticalObjectRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(PassRector::class);
    $services->set(AssertElementPresentRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertElementNotPresentRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertTextRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoTextRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertUniqueTextRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoUniqueTextRector::class);
    $services->set(AssertResponseRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertFieldByNameRector::class);
    $services->set(AssertNoFieldByNameRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertFieldByIdRector::class);
    $services->set(AssertFieldRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoFieldRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertRawRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoRawRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertTitleRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertLinkRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoLinkRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertLinkByHrefRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoLinkByHrefRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoFieldByIdRector::class);
    $services->set(AssertUrlRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertOptionRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertOptionByTextRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoOptionRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertOptionSelectedRector::class);
    $services->set(AssertFieldCheckedRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoFieldCheckedRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    // @todo AssertFieldByXPathRector
    // @todo AssertNoFieldByXPathRector
    // @todo AssertFieldsByValueRector
    $services->set(AssertEscapedRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoEscapedRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertPatternRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoPatternRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertCacheTagRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertNoCacheTagRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(AssertHeaderRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(BuildXPathQueryRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(ConstructFieldXpathRector::class);
    $services->set(GetRawContentRector::class);
    $services->set(GetAllOptionsRector::class);
    $services->set(UserPasswordRector::class);
};
