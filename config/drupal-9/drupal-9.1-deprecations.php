<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertElementNotPresentRector;
use DrupalRector\Rector\Deprecation\AssertElementPresentRector;
use DrupalRector\Rector\Deprecation\AssertEqualRector;
use DrupalRector\Rector\Deprecation\AssertEscapedRector;
use DrupalRector\Rector\Deprecation\AssertFieldByNameRector;
use DrupalRector\Rector\Deprecation\AssertHeaderRector;
use DrupalRector\Rector\Deprecation\AssertIdenticalObjectRector;
use DrupalRector\Rector\Deprecation\AssertIdenticalRector;
use DrupalRector\Rector\Deprecation\AssertLinkByHrefRector;
use DrupalRector\Rector\Deprecation\AssertLinkRector;
use DrupalRector\Rector\Deprecation\AssertNoCacheTagRector;
use DrupalRector\Rector\Deprecation\AssertNoEscapedRector;
use DrupalRector\Rector\Deprecation\AssertNoLinkByHrefRector;
use DrupalRector\Rector\Deprecation\AssertNoLinkRector;
use DrupalRector\Rector\Deprecation\AssertNoPatternRector;
use DrupalRector\Rector\Deprecation\AssertNoRawRector;
use DrupalRector\Rector\Deprecation\AssertNotEqualRector;
use DrupalRector\Rector\Deprecation\AssertNoTextRector;
use DrupalRector\Rector\Deprecation\AssertNotIdenticalRector;
use DrupalRector\Rector\Deprecation\AssertPatternRector;
use DrupalRector\Rector\Deprecation\AssertRawRector;
use DrupalRector\Rector\Deprecation\AssertRector;
use DrupalRector\Rector\Deprecation\AssertResponseRector;
use DrupalRector\Rector\Deprecation\AssertTextRector;
use DrupalRector\Rector\Deprecation\AssertTitleRector;
use DrupalRector\Rector\Deprecation\BuildXPathQueryRector;
use DrupalRector\Rector\Deprecation\UiHelperTraitDrupalPostFormRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(UiHelperTraitDrupalPostFormRector::class);
    // AssertLegactTrait items
    // @see https://www.drupal.org/project/rector/issues/3222671
    // @see https://www.drupal.org/node/3129738
    $services->set(AssertRector::class);
    $services->set(AssertEqualRector::class);
    $services->set(AssertNotEqualRector::class);
    $services->set(AssertIdenticalRector::class);
    $services->set(AssertNotIdenticalRector::class);
    $services->set(AssertIdenticalObjectRector::class);
    // @todo PassRector::class
    $services->set(AssertElementPresentRector::class);
    $services->set(AssertElementNotPresentRector::class);
    $services->set(AssertTextRector::class);
    $services->set(AssertNoTextRector::class);
    // @todo AssertUniqueTextRector::class
    // @todo AssertNoUniqueTextRector::class
    $services->set(AssertResponseRector::class);
    $services->set(AssertFieldByNameRector::class);
    // @todo AssertNoFieldByNameRector::class
    // @todo AssertFieldByIdRector::class
    // @todo AssertFieldRector::class
    // @todo AssertNoFieldRector::class
    $services->set(AssertRawRector::class);
    $services->set(AssertNoRawRector::class);
    $services->set(AssertTitleRector::class);
    $services->set(AssertLinkRector::class);
    $services->set(AssertNoLinkRector::class);
    $services->set(AssertLinkByHrefRector::class);
    $services->set(AssertNoLinkByHrefRector::class);
    // @todo AssertNoFieldByIdRector
    // @todo AssertUrlRector
    // @todo AssertOptionRector
    // @todo AssertOptionByTextRector
    // @todo AssertNoOptionRector
    // @todo AssertOptionSelectedRector
    // @todo AssertFieldCheckedRector
    // @todo AssertNoFieldCheckedRector
    // @todo AssertFieldByXPathRector
    // @todo AssertNoFieldByXPathRector
    // @todo AssertFieldsByValueRector
    $services->set(AssertEscapedRector::class);
    $services->set(AssertNoEscapedRector::class);
    $services->set(AssertPatternRector::class);
    $services->set(AssertNoPatternRector::class);
    $services->set(AssertCacheTagRector::class);
    $services->set(AssertNoCacheTagRector::class);
    $services->set(AssertHeaderRector::class);
    $services->set(BuildXPathQueryRector::class);
    // @todo ConstructFieldXpathRector
    // @todo GetRawContentRector
    // @todo GetAllOptionsRector
};
