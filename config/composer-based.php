<?php

declare(strict_types=1);

use Composer\Semver\Semver;
use DrupalRector\Drupal10\Rector\Deprecation\ReplaceModuleHandlerGetNameRector;
use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRebuildThemeDataRector;
use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRequestTimeConstantRector;
use DrupalRector\Drupal10\Rector\Deprecation\SystemTimeZonesRector;
use DrupalRector\Drupal10\Rector\Deprecation\WatchdogExceptionRector;
use DrupalRector\Drupal11\Rector\Deprecation\BlockContentSelectionExtendsRector;
use DrupalRector\Drupal11\Rector\Deprecation\BlockContentTestBaseStringToArrayRector;
use DrupalRector\Drupal11\Rector\Deprecation\CheckMarkupToProcessedTextRector;
use DrupalRector\Drupal11\Rector\Deprecation\CommentLinkBuilderConstructorRector;
use DrupalRector\Drupal11\Rector\Deprecation\DeprecatedFilterFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\DrupalGetHeadersAssocArrayRector;
use DrupalRector\Drupal11\Rector\Deprecation\EntityFormModeEmptyDescriptionToNullRector;
use DrupalRector\Drupal11\Rector\Deprecation\ErrorCurrentErrorHandlerRector;
use DrupalRector\Drupal11\Rector\Deprecation\FileManagedFileSubmitRector;
use DrupalRector\Drupal11\Rector\Deprecation\FileSystemBasenameToNativeRector;
use DrupalRector\Drupal11\Rector\Deprecation\FilterFormatFunctionsToServiceRector;
use DrupalRector\Drupal11\Rector\Deprecation\GetDrupalRootToRootPropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\GetNameToNameRector;
use DrupalRector\Drupal11\Rector\Deprecation\GetOriginalClassToGetDecoratedClassesRector;
use DrupalRector\Drupal11\Rector\Deprecation\HookRequirementsAlterRenameRector;
use DrupalRector\Drupal11\Rector\Deprecation\LoadAllIncludesRector;
use DrupalRector\Drupal11\Rector\Deprecation\LocaleCompareIncToServiceRector;
use DrupalRector\Drupal11\Rector\Deprecation\MediaFilterFormatEditFormValidateRector;
use DrupalRector\Drupal11\Rector\Deprecation\MigrateSqlGetMigrationPluginManagerRector;
use DrupalRector\Drupal11\Rector\Deprecation\MovePointerToMouseOverRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeAccessRebuildFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\NodeStorageDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\PluginBaseIsConfigurableRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveAutomatedCronSubmitHandlerRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveCacheExpireOverrideRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveCacheTagChecksumAssertionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveConfigSaveTrustedDataArgRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveDrupalToStringTraitRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveFilterTipsLongParamRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveHandlerBaseDefineExtraOptionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveInstallSchemaSystemSequencesRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveLinkWidgetValidateTitleElementRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerAddModuleCallsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemovePhpUnitCompatibilityTraitRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRendererAddCacheableDependencyNonObjectRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRootFromConvertDbUrlRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRootFromCreateConnectionOptionsFromUrlRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveRouteBuilderDeprecatedArgsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveSetUriCallbackRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveSourceModuleFromMigrateSourceAttributeRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveStateCacheSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveToolkitArgFromImageToolkitOperationConstructorRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveTrustDataCallRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveTwigNodeTransTagArgumentRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveUpdaterPostInstallMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveViewsRowCacheKeysRector;
use DrupalRector\Drupal11\Rector\Deprecation\RenameHookRankingRector;
use DrupalRector\Drupal11\Rector\Deprecation\RenameStopProceduralHookScanRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceAddCachedDiscoveryMethodCallRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceAlphadecimalToIntNullRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentManagerGetCountNewCommentsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentPreviewConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceDateTimeRangeConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceDialogClassOptionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceDrupalStaticResetFileReferencesRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEditorLoadRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEntityOriginalPropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceEntityReferenceRecursiveLimitRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceExpectDeprecationRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceFieldgroupToFieldsetRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceHideShowWithPrintedRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceItemAttributesWithAttributesRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleBatchProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleConfigBatchFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleTranslationPathConfigRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAccessViewAllNodesRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAddBodyFieldRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeModuleProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeSetPreviewModeRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeViewControllerRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNonBoolAccessRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplacePdoFetchConstantsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceRecipeRunnerInstallModuleRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSessionManagerDeleteRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSessionWritesWithRequestSessionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceSystemPerformanceGzipKeyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceThemeGetSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceTwigExtensionRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserOneTimeAuthFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserSessionNamePropertyRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceViewsProceduralFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\StatementPrefetchIteratorFetchColumnRector;
use DrupalRector\Drupal11\Rector\Deprecation\StripMigrationDependenciesExpandArgRector;
use DrupalRector\Drupal11\Rector\Deprecation\SystemRegionFunctionsRector;
use DrupalRector\Drupal11\Rector\Deprecation\SystemSortThemesRector;
use DrupalRector\Drupal11\Rector\Deprecation\TaxonomyTermPageVariableToViewModeRector;
use DrupalRector\Drupal11\Rector\Deprecation\UploadedFileConstraintArrayOptionsToNamedArgsRector;
use DrupalRector\Drupal11\Rector\Deprecation\UseEntityTypeHasIntegerIdRector;
use DrupalRector\Drupal11\Rector\Deprecation\UserLoadByNameAndMailRector;
use DrupalRector\Drupal11\Rector\Deprecation\ViewsBlockItemsPerPageNoneToNullRector;
use DrupalRector\Drupal11\Rector\Deprecation\ViewsConfigUpdaterClassResolverToServiceRector;
use DrupalRector\Drupal11\Rector\Deprecation\ViewsPluginHandlerManagerRector;
use DrupalRector\Drupal12\Rector\Deprecation\AddSymfonyConstraintValidatorTypeDeclarationsRector;
use DrupalRector\Drupal8\Rector\Deprecation\DBRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalLRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalServiceRenameRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalSetMessageRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalURLRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityCreateRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityInterfaceLinkRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityLoadRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityViewRector;
use DrupalRector\Drupal8\Rector\Deprecation\FileDefaultSchemeRector;
use DrupalRector\Drupal8\Rector\Deprecation\FunctionalTestDefaultThemePropertyRector;
use DrupalRector\Drupal8\Rector\Deprecation\GetMockRector;
use DrupalRector\Drupal8\Rector\Deprecation\LinkGeneratorTraitLRector;
use DrupalRector\Drupal8\Rector\Deprecation\RequestTimeConstRector;
use DrupalRector\Drupal8\Rector\Deprecation\SafeMarkupFormatRector;
use DrupalRector\Drupal8\Rector\Deprecation\StaticToFunctionRector;
use DrupalRector\Drupal8\Rector\ValueObject\DBConfiguration;
use DrupalRector\Drupal8\Rector\ValueObject\DrupalServiceRenameConfiguration;
use DrupalRector\Drupal8\Rector\ValueObject\EntityLoadConfiguration;
use DrupalRector\Drupal8\Rector\ValueObject\GetMockConfiguration;
use DrupalRector\Drupal8\Rector\ValueObject\StaticToFunctionConfiguration;
use DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByIdRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertFieldByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertLegacyTraitRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByIdRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoFieldByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Drupal9\Rector\Deprecation\AssertOptionSelectedRector;
use DrupalRector\Drupal9\Rector\Deprecation\ConstructFieldXpathRector;
use DrupalRector\Drupal9\Rector\Deprecation\ExtensionPathRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileCreateUrlRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileUrlTransformRelativeRector;
use DrupalRector\Drupal9\Rector\Deprecation\FromUriRector;
use DrupalRector\Drupal9\Rector\Deprecation\FunctionToEntityTypeStorageMethod;
use DrupalRector\Drupal9\Rector\Deprecation\GetAllOptionsRector;
use DrupalRector\Drupal9\Rector\Deprecation\GetRawContentRector;
use DrupalRector\Drupal9\Rector\Deprecation\ModuleLoadRector;
use DrupalRector\Drupal9\Rector\Deprecation\PassRector;
use DrupalRector\Drupal9\Rector\Deprecation\SystemSortByInfoNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyTermLoadMultipleByNameRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesDrupalStaticResetRector;
use DrupalRector\Drupal9\Rector\Deprecation\TaxonomyVocabularyGetNamesRector;
use DrupalRector\Drupal9\Rector\Deprecation\UiHelperTraitDrupalPostFormRector;
use DrupalRector\Drupal9\Rector\Deprecation\UserPasswordRector;
use DrupalRector\Drupal9\Rector\Property\ProtectedStaticModulesPropertyRector;
use DrupalRector\Drupal9\Rector\ValueObject\AssertLegacyTraitConfiguration;
use DrupalRector\Drupal9\Rector\ValueObject\ExtensionPathConfiguration;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\ConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionCallRemovalRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector;
use DrupalRector\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;
use DrupalRector\Rector\PHPUnit\ShouldCallParentMethodsRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionCallRemovalConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Composer\InstalledPackageResolver;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\PublicDataProviderClassMethodRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\PHPUnit100\Rector\MethodCall\RemoveSetMethodsMethodCallRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\ValueObject\RenameStaticMethod;

/**
 * Every drupal-rector rule, bound to the exact `drupal/core` version its
 * deprecation was introduced in.
 *
 * Deliberately duplicates the registrations of the per-minor configs in
 * config/drupal-N: a rule is registered here and there. Keeping this a plain
 * config of real rules is worth the duplication — the constraint of every rule
 * is readable on the spot, instead of being derived from the file a
 * registration happens to live in. `ComposerBasedSetTest` fails when a rule of a
 * per-minor config is missing here.
 *
 * Instead of picking set lists by hand, this set lets Rector pick the rules from
 * the installed `drupal/core` version: a site on 11.2 gets the rules bound to
 * `>=8.0.0` through `>=11.2.0` and never a later minor's. Because the installed
 * core is known exactly, the otherwise opt-in "breaking" renames (whose
 * replacement symbol only exists from a given minor onward) are safe to include
 * — they cannot fatal on a core that is guaranteed to have the replacement.
 *
 * Usage:
 *
 *     return RectorConfig::configure()
 *         ->withComposerBased(drupal: true);
 *
 * The Symfony and PHPUnit version sets that the per-minor Drupal configs pull in
 * are deliberately not repeated here: those packages ship their own
 * composer-based sets, bound to their own installed version, which is more
 * accurate than inferring them from the Drupal minor. Add
 * `symfony: true, phpunit: true` to the call above to get those too.
 *
 * To look ahead and prepare for a Drupal version you have not installed yet, use
 * the explicit \DrupalRector\Set\Drupal11SetList sets instead — they are not
 * version-bound.
 *
 * @see \DrupalRector\Set\DrupalSetList::COMPOSER_BASED
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, fn (): AddCommentService => new AddCommentService());

    $installedCoreVersion = (new InstalledPackageResolver())->resolvePackageVersion('drupal/core');

    // the bootstrap resolves the Drupal root and the PHPUnit bootstrap file, which
    // only exist when the analysed project actually has Drupal installed
    if ($installedCoreVersion !== null) {
        $rectorConfig->import(__DIR__.'/drupal-bootstrap.php');
    }

    /**
     * Registers a rule that takes no configuration, and so cannot be registered
     * with ruleWithConfigurationComposerVersionBound(), only when the installed
     * `drupal/core` satisfies the constraint.
     *
     * @param class-string<\Rector\Contract\Rector\RectorInterface> $rectorClass
     */
    $ruleSince = static function (string $rectorClass, string $constraint) use ($rectorConfig, $installedCoreVersion): void {
        if ($installedCoreVersion === null) {
            return;
        }

        if (!Semver::satisfies($installedCoreVersion, $constraint)) {
            return;
        }

        $rectorConfig->rule($rectorClass);
    };

    // ---------------------------------------------------------------------
    // Drupal 8.0
    // ---------------------------------------------------------------------

    $rectorConfig->ruleWithConfigurationComposerVersionBound(DBRector::class, [
        // https://www.drupal.org/node/2993033
        new DBConfiguration('db_delete', 2),
        new DBConfiguration('db_insert', 2),
        new DBConfiguration('db_query', 3),
        new DBConfiguration('db_select', 3),
        new DBConfiguration('db_update', 2),
    ], 'drupal/core', '>=8.0.0');

    $ruleSince(DrupalURLRector::class, '>=8.0.0');

    $ruleSince(DrupalLRector::class, '>=8.0.0');

    $ruleSince(EntityCreateRector::class, '>=8.0.0');

    $ruleSince(EntityDeleteMultipleRector::class, '>=8.0.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        // https://www.drupal.org/node/2418133
        new FunctionToServiceConfiguration('8.0.0', 'drupal_realpath', 'file_system', 'realpath'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('8.0.0', 'drupal_render', 'renderer', 'render'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('8.0.0', 'drupal_render_root', 'renderer', 'renderRoot'),
        // https://www.drupal.org/node/1876852
        new FunctionToServiceConfiguration('8.0.0', 'format_date', 'date.formatter', 'format'),
    ], 'drupal/core', '>=8.0.0');

    $ruleSince(EntityInterfaceLinkRector::class, '>=8.0.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        // https://www.drupal.org/node/2614344
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl', '8.0.0'),
    ], 'drupal/core', '>=8.0.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(EntityLoadRector::class, [
        new EntityLoadConfiguration('entity'),
        new EntityLoadConfiguration('file'),
        new EntityLoadConfiguration('node'),
        new EntityLoadConfiguration('user'),
    ], 'drupal/core', '>=8.0.0');

    $ruleSince(EntityViewRector::class, '>=8.0.0');

    $ruleSince(EntityManagerRector::class, '>=8.0.0');

    $ruleSince(LinkGeneratorTraitLRector::class, '>=8.0.0');

    $ruleSince(SafeMarkupFormatRector::class, '>=8.0.0');

    // ---------------------------------------------------------------------
    // Drupal 8.2
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/2418133
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration(
            '8.2.0',
            'file_directory_os_temp',
            'Drupal\Component\FileSystem\FileSystem',
            'getOsTemporaryDirectory'
        ),
    ], 'drupal/core', '>=8.2.0');

    // ---------------------------------------------------------------------
    // Drupal 8.3
    // ---------------------------------------------------------------------

    $ruleSince(RequestTimeConstRector::class, '>=8.3.0');

    // ---------------------------------------------------------------------
    // Drupal 8.4
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/2907725
    $rectorConfig->ruleWithConfigurationComposerVersionBound(GetMockRector::class, [
        new GetMockConfiguration('Drupal\Tests\BrowserTestBase'),
        new GetMockConfiguration('Drupal\KernelTests\KernelTestBase'),
        new GetMockConfiguration('Drupal\Tests\UnitTestCase'),
    ], 'drupal/core', '>=8.4.0');

    // ---------------------------------------------------------------------
    // Drupal 8.5
    // ---------------------------------------------------------------------

    $ruleSince(DrupalSetMessageRector::class, '>=8.5.0');

    /*
     * Replaces deprecated DATETIME_DATE_STORAGE_FORMAT, DATETIME_DATETIME_STORAGE_FORMAT, DATETIME_STORAGE_TIMEZONE constant use.
     *
     * See https://www.drupal.org/node/2912980 for change record.
     */
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('DATETIME_DATE_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATE_STORAGE_FORMAT', '8.5.0'),
        new ConstantToClassConfiguration('DATETIME_DATETIME_STORAGE_FORMAT', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'DATETIME_STORAGE_FORMAT', '8.5.0'),
        new ConstantToClassConfiguration('DATETIME_STORAGE_TIMEZONE', 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface', 'STORAGE_TIMEZONE', '8.5.0'),
    ], 'drupal/core', '>=8.5.0');

    // ---------------------------------------------------------------------
    // Drupal 8.6
    // ---------------------------------------------------------------------

    $rectorConfig->ruleWithConfigurationComposerVersionBound(StaticToFunctionRector::class, [
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strlen', 'mb_strlen'),
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strtolower', 'mb_strtolower'),
        // https://www.drupal.org/node/2850048
        new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'substr', 'mb_substr'),
    ], 'drupal/core', '>=8.6.0');

    // ---------------------------------------------------------------------
    // Drupal 8.7
    // ---------------------------------------------------------------------

    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        // https://www.drupal.org/node/3006851
        new FunctionToServiceConfiguration('8.7.0', 'file_prepare_directory', 'file_system', 'prepareDirectory'),
        // https://www.drupal.org/node/3006851
        new FunctionToServiceConfiguration('8.7.0', 'file_unmanaged_save_data', 'file_system', 'saveData'),
    ], 'drupal/core', '>=8.7.0');

    /**
     * Replaces deprecated FILE_CREATE_DIRECTORY constant use.
     *
     * No change record found.
     */
    $constantToClassFileCreateDirectory = new ConstantToClassConfiguration('FILE_CREATE_DIRECTORY', 'Drupal\Core\File\FileSystemInterface', 'CREATE_DIRECTORY', '8.7.0');

    /**
     * Replaces deprecated FILE_EXISTS_REPLACE, FILE_EXISTS_RENAME constant use.
     *
     * See https://www.drupal.org/node/3006851 for change record.
     */
    $constantToClassFileExistReplace = new ConstantToClassConfiguration('FILE_EXISTS_REPLACE', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_REPLACE', '8.7.0');

    $constantToClassFileExistsRename = new ConstantToClassConfiguration('FILE_EXISTS_RENAME', 'Drupal\Core\File\FileSystemInterface', 'EXISTS_RENAME', '8.7.0');

    /**
     * Replaces deprecated FILE_MODIFY_PERMISSIONS constant use.
     *
     * No change record found.
     */
    $constantToClassFileModifyPermissions = new ConstantToClassConfiguration('FILE_MODIFY_PERMISSIONS', 'Drupal\Core\File\FileSystemInterface', 'MODIFY_PERMISSIONS', '8.7.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        $constantToClassFileCreateDirectory,
        $constantToClassFileExistReplace,
        $constantToClassFileExistsRename,
        $constantToClassFileModifyPermissions,
    ], 'drupal/core', '>=8.7.0');

    // ---------------------------------------------------------------------
    // Drupal 8.8
    // ---------------------------------------------------------------------

    $rectorConfig->ruleWithConfigurationComposerVersionBound(DrupalServiceRenameRector::class, [
        new DrupalServiceRenameConfiguration('path.alias_repository', 'path_alias.repository'),
        new DrupalServiceRenameConfiguration('path.alias_whitelist', 'path_alias.whitelist'),
        new DrupalServiceRenameConfiguration('path_processor_alias', 'path_alias.path_processor'),
        new DrupalServiceRenameConfiguration('path_subscriber', 'path_alias.subscriber'),
        new DrupalServiceRenameConfiguration('path.alias_manager', 'path_alias.manager'),
    ], 'drupal/core', '>=8.8.0');

    $ruleSince(FileDefaultSchemeRector::class, '>=8.8.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class,
        [
            // https://www.drupal.org/node/2835616
            new FunctionToServiceConfiguration('8.8.0', 'entity_get_display', 'entity_display.repository', 'getViewDisplay'),
            // https://www.drupal.org/node/2835616
            new FunctionToServiceConfiguration('8.8.0', 'entity_get_form_display', 'entity_display.repository', 'getFormDisplay'),
            // https://www.drupal.org/node/3039255
            new FunctionToServiceConfiguration('8.8.0', 'file_directory_temp', 'file_system', 'getTempDirectory'),
            // https://www.drupal.org/node/3038437
            new FunctionToServiceConfiguration('8.8.0', 'file_scan_directory', 'file_system', 'scanDirectory'),
            // https://www.drupal.org/node/3035273
            new FunctionToServiceConfiguration('8.8.0', 'file_uri_target', 'stream_wrapper_manager', 'getTarget'),
        ], 'drupal/core', '>=8.8.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        // https://www.drupal.org/node/3075567
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityTypeInterface', 'getLowercaseLabel', 'getSingularLabel', '8.8.0'),
    ], 'drupal/core', '>=8.8.0');

    // https://www.drupal.org/node/3083055
    $ruleSince(FunctionalTestDefaultThemePropertyRector::class, '>=8.8.0');

    // ---------------------------------------------------------------------
    // Drupal 9.0
    // ---------------------------------------------------------------------

    $ruleSince(ProtectedStaticModulesPropertyRector::class, '>=9.0.0');

    $ruleSince(ShouldCallParentMethodsRector::class, '>=9.0.0');

    // ---------------------------------------------------------------------
    // Drupal 9.1
    // ---------------------------------------------------------------------

    $ruleSince(UiHelperTraitDrupalPostFormRector::class, '>=9.1.0');

    $ruleSince(PassRector::class, '>=9.1.0');

    $ruleSince(AssertNoUniqueTextRector::class, '>=9.1.0');

    $ruleSince(AssertFieldByNameRector::class, '>=9.1.0');

    $ruleSince(AssertNoFieldByNameRector::class, '>=9.1.0');

    $ruleSince(AssertFieldByIdRector::class, '>=9.1.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(AssertLegacyTraitRector::class, [
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
    ], 'drupal/core', '>=9.1.0');

    $ruleSince(AssertNoFieldByIdRector::class, '>=9.1.0');

    $ruleSince(AssertOptionSelectedRector::class, '>=9.1.0');

    $ruleSince(ConstructFieldXpathRector::class, '>=9.1.0');

    $ruleSince(GetRawContentRector::class, '>=9.1.0');

    $ruleSince(GetAllOptionsRector::class, '>=9.1.0');

    $ruleSince(UserPasswordRector::class, '>=9.1.0');

    // Change record: https://www.drupal.org/node/3162663
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameStaticMethodRector::class, [
        new RenameStaticMethod(
            'Drupal\Component\Utility\Bytes',
            'toInt',
            'Drupal\Component\Utility\Bytes',
            'toNumber'
        ),
    ], 'drupal/core', '>=9.1.0');

    // Change record: https://www.drupal.org/node/3151009 (only constants are supported)
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            '9.1.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            '9.1.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            '9.1.0',
        ),
    ], 'drupal/core', '>=9.1.0');

    // ---------------------------------------------------------------------
    // Drupal 9.2
    // ---------------------------------------------------------------------

    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        // https://www.drupal.org/node/3187914
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Session\MetadataBag', 'clearCsrfTokenSeed', 'stampNew', '9.2.0'),
    ], 'drupal/core', '>=9.2.0');

    // ---------------------------------------------------------------------
    // Drupal 9.3
    // ---------------------------------------------------------------------

    // Change record: https://www.drupal.org/node/2940438.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ExtensionPathRector::class, [
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
    ], 'drupal/core', '>=9.3.0');

    // Change record: https://www.drupal.org/node/2940031
    $ruleSince(FileCreateUrlRector::class, '>=9.3.0');

    $ruleSince(FileUrlTransformRelativeRector::class, '>=9.3.0');

    $ruleSince(FromUriRector::class, '>=9.3.0');

    // Change record: https://www.drupal.org/node/3223520
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('9.3.0', 'file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('9.3.0', 'file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('9.3.0', 'file_save_data', 'file.repository', 'writeData'),
        // Change record: https://www.drupal.org/node/2939099
        new FunctionToServiceConfiguration('9.3.0', 'render', 'renderer', 'render'),
    ], 'drupal/core', '>=9.3.0');

    // Change record: https://www.drupal.org/node/3223091.
    $ruleSince(FileBuildUriRector::class, '>=9.3.0');

    // Change record: https://www.drupal.org/node/3225999
    $ruleSince(SystemSortByInfoNameRector::class, '>=9.3.0');

    // Change rector: https://www.drupal.org/node/3039041
    // Missing: $url = $term->toUrl(); AND $name = taxonomy_term_title($term); AND taxonomy_implode_tags();
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToEntityTypeStorageMethod::class, [
        new FunctionToEntityTypeStorageConfiguration('taxonomy_terms_static_reset', 'taxonomy_term', 'resetCache'),
        new FunctionToEntityTypeStorageConfiguration('taxonomy_vocabulary_static_reset', 'taxonomy_vocabulary', 'resetCache'),
    ], 'drupal/core', '>=9.3.0');

    $ruleSince(TaxonomyVocabularyGetNamesRector::class, '>=9.3.0');

    $ruleSince(TaxonomyTermLoadMultipleByNameRector::class, '>=9.3.0');

    $ruleSince(TaxonomyVocabularyGetNamesDrupalStaticResetRector::class, '>=9.3.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('9.3.0', 'taxonomy_implode_tags', 'Drupal\Core\Entity\Element\EntityAutocomplete', 'getEntityLabels'),
    ], 'drupal/core', '>=9.3.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(\DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector::class, [
        new \DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
        new \DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration('taxonomy_term_title', 'label'),
    ], 'drupal/core', '>=9.3.0');

    // Change record: https://www.drupal.org/node/3022147
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration(
            'FILE_STATUS_PERMANENT',
            'Drupal\file\FileInterface',
            'STATUS_PERMANENT',
            '9.3.0',
        ),
    ], 'drupal/core', '>=9.3.0');

    // ---------------------------------------------------------------------
    // Drupal 9.4
    // ---------------------------------------------------------------------

    // Change record https://www.drupal.org/node/3220952
    $ruleSince(ModuleLoadRector::class, '>=9.4.0');

    // ---------------------------------------------------------------------
    // Drupal 10.0
    // ---------------------------------------------------------------------

    $ruleSince(ShouldCallParentMethodsRector::class, '>=10.0.0');

    // ---------------------------------------------------------------------
    // Drupal 10.1
    // ---------------------------------------------------------------------

    // PHPUnit 10.0 rules
    $ruleSince(PublicDataProviderClassMethodRector::class, '>=10.1.0');
    $ruleSince(StaticDataProviderClassMethodRector::class, '>=10.1.0');
    $ruleSince(RemoveSetMethodsMethodCallRector::class, '>=10.1.0');

    // https://www.drupal.org/node/3244583
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
    ], 'drupal/core', '>=10.1.0');

    // https://www.drupal.org/node/2932520
    $rectorConfig->ruleWithConfigurationComposerVersionBound(WatchdogExceptionRector::class, [
        new DrupalIntroducedVersionConfiguration('10.1.0'),
    ], 'drupal/core', '>=10.1.0');

    // https://www.drupal.org/node/3023528
    $rectorConfig->ruleWithConfigurationComposerVersionBound(SystemTimeZonesRector::class, [
        new DrupalIntroducedVersionConfiguration('10.1.0'),
    ], 'drupal/core', '>=10.1.0');

    // ---------------------------------------------------------------------
    // Drupal 10.2
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/2999981
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
    ], 'drupal/core', '>=10.2.0');

    // https://www.drupal.org/node/3265963
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'getResource', 'getImage', '10.2.0'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'setResource', 'setImage', '10.2.0'),
    ], 'drupal/core', '>=10.2.0');

    // https://www.drupal.org/node/3358337
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
    ], 'drupal/core', '>=10.2.0');

    // ---------------------------------------------------------------------
    // Drupal 10.3
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3407994
    // RendererInterface::renderPlain() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by RendererInterface::renderInIsolation().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Render\RendererInterface', 'renderPlain', 'renderInIsolation', '10.3.0'),
    ], 'drupal/core', '>=10.3.0');

    // https://www.drupal.org/node/3411269 file_icon_class, file_icon_map
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
    ], 'drupal/core', '>=10.3.0');

    // https://www.drupal.org/node/3413196
    // ThemeHandlerInterface::rebuildThemeData() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service('extension.list.theme')->reset()->getList().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceRebuildThemeDataRector::class, [
        new DrupalIntroducedVersionConfiguration('10.3.0'),
    ], 'drupal/core', '>=10.3.0');

    // https://www.drupal.org/node/3310017
    // ModuleHandlerInterface::getName() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceModuleHandlerGetNameRector::class, [
        new DrupalIntroducedVersionConfiguration('10.3.0'),
    ], 'drupal/core', '>=10.3.0');

    // https://www.drupal.org/node/3426517
    // https://www.drupal.org/node/3575575
    // FileSystemInterface::EXISTS_* deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\File\FileExists enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_RENAME',
            'Drupal\Core\File\FileExists',
            'Rename',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_ERROR',
            'Drupal\Core\File\FileExists',
            'Error',
            '10.3.0',
        ),
    ], 'drupal/core', '>=10.3.0');

    // ---------------------------------------------------------------------
    // Drupal 11.0
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3217904
    // TestCase::getName() deprecated in drupal:10.1.0, removed in drupal:11.0.0.
    // Replaced by name().
    $ruleSince(GetNameToNameRector::class, '>=11.0.0');

    // https://www.drupal.org/node/3436954
    // https://www.drupal.org/node/2575105 (change record)
    // $settings['state_cache'] deprecated in drupal:11.0.0.
    // State caching is now permanently enabled and the setting has no effect.
    $ruleSince(RemoveStateCacheSettingRector::class, '>=11.0.0');

    // https://www.drupal.org/node/3395986
    // REQUEST_TIME constant deprecated in drupal:8.3.0, removed in drupal:11.0.0.
    // Replaced by \Drupal::time()->getRequestTime().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceRequestTimeConstantRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ], 'drupal/core', '>=11.0.0');

    // https://www.drupal.org/node/3574717
    // https://www.drupal.org/node/3442785 (change record)
    // getMigrationDependencies($expand) deprecated in drupal:11.0.0, removed in drupal:12.0.0.
    // The $expand boolean argument is removed; call without arguments.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(StripMigrationDependenciesExpandArgRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ], 'drupal/core', '>=11.0.0');

    // https://www.drupal.org/node/3439369
    // https://www.drupal.org/node/3282894 (change record)
    // Sql::getMigrationPluginManager() deprecated in drupal:9.5.0, removed in drupal:11.0.0.
    // Replaced by $this->migrationPluginManager property access.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MigrateSqlGetMigrationPluginManagerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ], 'drupal/core', '>=11.0.0');

    // https://www.drupal.org/node/3417066 (@group legacy → #[IgnoreDeprecations])
    // https://www.drupal.org/project/drupal/issues/3535662 (annotations → attributes)
    // PHPUnit 12 (Drupal 12) drops annotation metadata in favour of attributes.
    // Backward-compatible: under BC-on / Drupal < 12 the annotation is kept
    // alongside the new attribute (unknown attribute classes are ignored on
    // PHPUnit 9/10/11); only a D12 install or an opted-in clean rewrite strips it.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(PhpUnitTestAnnotationToAttributeRector::class, [
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'dataProvider', 'PHPUnit\Framework\Attributes\DataProvider'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'depends', 'PHPUnit\Framework\Attributes\Depends'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'testWith', 'PHPUnit\Framework\Attributes\TestWith'),
    ], 'drupal/core', '>=11.0.0');

    // Forward-compat Symfony 8 / Drupal 12 signature change (NOT an 11.0
    // deprecation). Registered here so it fires on drupal/core ^11.0 (every D11
    // install) — the change is backward compatible, so applying it while still
    // on D11 is safe and prepares the module for D12. Also registered in the
    // drupal-12.0 set for installs already on ^12.0.
    // https://git.drupalcode.org/project/redirect/-/merge_requests/200
    $ruleSince(AddSymfonyConstraintValidatorTypeDeclarationsRector::class, '>=11.0.0');

    // ---------------------------------------------------------------------
    // Drupal 11.1
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3459533
    // https://www.drupal.org/node/2946122 (change record)
    // PluginBase::isConfigurable() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by instanceof \Drupal\Component\Plugin\ConfigurableInterface.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(PluginBaseIsConfigurableRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ], 'drupal/core', '>=11.1.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\Core\Routing\MatchingRouteNotFoundException' => 'Symfony\Component\Routing\Exception\ResourceNotFoundException',
        'Drupal\Core\Asset\LibraryDiscovery' => 'Drupal\Core\Asset\LibraryDiscoveryInterface',
        'Drupal\user\Entity\EntityPermissionsRouteProviderWithCheck' => 'Drupal\user\Entity\EntityPermissionsRouteProvider',
    ], 'drupal/core', '>=11.1.0');

    // The MethodToMethodWithCheckRector entry below
    // for AliasManager::pathAliasWhitelistRebuild() → pathAliasPrefixListRebuild()
    // is BC-wrapped and remains in this default set.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild', '11.1.0'),
    ], 'drupal/core', '>=11.1.0');

    // https://www.drupal.org/node/3442009
    // https://www.drupal.org/node/3368812 (change record)
    // ModuleHandlerInterface::writeCache() deprecated in drupal:11.1.0, removed in drupal:12.0.0. No replacement needed.
    // ModuleHandlerInterface::getHookInfo() deprecated in drupal:11.1.0, removed in drupal:12.0.0. Replaced by [].
    $ruleSince(RemoveModuleHandlerDeprecatedMethodsRector::class, '>=11.1.0');

    // https://www.drupal.org/node/3575254
    // locale_config_batch_set_config_langcodes() and locale_config_batch_refresh_name() deprecated
    // in drupal:11.1.0, removed in drupal:12.0.0. Renamed to update_default_config_langcodes
    // and update_config_translations respectively.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceLocaleConfigBatchFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ], 'drupal/core', '>=11.1.0');

    // https://www.drupal.org/node/3417136
    // https://www.drupal.org/node/3461934 (change record)
    // Updater::postInstall() and postInstallTasks() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // The entire install-via-URL flow was eliminated; overrides are dead code.
    $ruleSince(RemoveUpdaterPostInstallMethodsRector::class, '>=11.1.0');

    // https://www.drupal.org/node/3196937
    // https://www.drupal.org/node/3473739 (change record)
    // BlockContentTestBase::createBlockContentType() $values deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Callers must pass an explicit array such as ['id' => 'basic'] instead of a plain string.
    $ruleSince(BlockContentTestBaseStringToArrayRector::class, '>=11.1.0');

    // https://www.drupal.org/node/3421202
    // https://www.drupal.org/node/3460567 (change record)
    // movePointerTo() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by getSession()->getDriver()->mouseOver() with an XPath selector.
    $ruleSince(MovePointerToMouseOverRector::class, '>=11.1.0');

    // https://www.drupal.org/node/3432827
    // https://www.drupal.org/node/3442229 (change record)
    // addMethodCall('addCachedDiscovery', ...) on plugin.cache_clearer deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by the plugin_manager_cache_clear tag approach.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceAddCachedDiscoveryMethodCallRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ], 'drupal/core', '>=11.1.0');

    // https://www.drupal.org/node/3488176
    // drupal_common_theme() removed in drupal:11.1.0.
    // Replaced by \Drupal\Core\Theme\ThemeCommonElements::commonElements().
    // https://www.drupal.org/node/3574424 (digest issue)
    // https://www.drupal.org/node/3268441 (change record)
    // image_filter_keyword() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Component\Utility\Image::getKeywordOffset().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.1.0', 'drupal_common_theme', 'Drupal\Core\Theme\ThemeCommonElements', 'commonElements'),
        new FunctionToStaticConfiguration('11.1.0', 'image_filter_keyword', 'Drupal\Component\Utility\Image', 'getKeywordOffset'),
    ], 'drupal/core', '>=11.1.0');

    // https://www.drupal.org/node/3440169
    // https://www.drupal.org/node/3456178 (change record: integer-keyed headers)
    // https://www.drupal.org/node/3456233 (change record: null header values)
    // UiHelperTrait::drupalGet() $headers as indexed colon-separated strings or null values
    // deprecated in drupal:11.1.0, removed in drupal:12.0.0. Replaced by the associative array
    // format ['Header-Name' => 'value'], with empty strings in place of null.
    $ruleSince(DrupalGetHeadersAssocArrayRector::class, '>=11.1.0');

    // ---------------------------------------------------------------------
    // Drupal 11.1 (breaking)
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3151086
    // https://www.drupal.org/node/3467559 (change record)
    // AliasWhitelist and AliasWhitelistInterface deprecated in drupal:11.1.0,
    // removed in drupal:12.0.0. Replaced by AliasPrefixList and
    // AliasPrefixListInterface, which were introduced in 11.1.0 and do NOT
    // exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\path_alias\AliasWhitelist' => 'Drupal\path_alias\AliasPrefixList',
        'Drupal\path_alias\AliasWhitelistInterface' => 'Drupal\path_alias\AliasPrefixListInterface',
    ], 'drupal/core', '>=11.1.0');

    // ---------------------------------------------------------------------
    // Drupal 11.2
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3490200
    // https://www.drupal.org/node/3490312 (change record)
    // StatementPrefetchIterator::fetchColumn() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by fetchField().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(StatementPrefetchIteratorFetchColumnRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3500622
    // CacheBackendInterface::invalidateAll() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by deleteAll().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Cache\CacheBackendInterface', 'invalidateAll', 'deleteAll', '11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3504125
    // template_preprocess_*() functions deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by ThemePreprocess and DatePreprocess service methods.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_time', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessTime'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_form', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeForm'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_wrapper', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeWrapper'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_links', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessLinks'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_container', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessContainer'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_html', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessHtml'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessPage'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3501136
    // template_preprocess() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // https://www.drupal.org/node/3522119
    // update_clear_update_disk_cache(), update_delete_file_if_stale(),
    // _update_manager_cache_directory(), _update_manager_extract_directory(),
    // and _update_manager_unique_identifier() deprecated in drupal:11.2.0, removed in drupal:13.0.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('template_preprocess'),
        new FunctionCallRemovalConfiguration('update_clear_update_disk_cache'),
        new FunctionCallRemovalConfiguration('update_delete_file_if_stale'),
        new FunctionCallRemovalConfiguration('_update_manager_cache_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_extract_directory'),
        new FunctionCallRemovalConfiguration('_update_manager_unique_identifier'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3528899
    // https://www.drupal.org/node/3550193 (change record)
    // ModuleHandlerInterface::addModule() and addProfile() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // These methods are no-ops and can be removed.
    $ruleSince(RemoveModuleHandlerAddModuleCallsRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3485084
    // https://www.drupal.org/node/3486781 (change record)
    // HandlerBase::defineExtraOptions() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // No replacement — Drupal core never called it; any override is dead code.
    $ruleSince(RemoveHandlerBaseDefineExtraOptionsRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3410938
    // drupal_requirements_severity() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by RequirementSeverity::maxSeverityFromRequirements().
    // https://www.drupal.org/node/3495966
    // https://www.drupal.org/node/3497049 (change record)
    // entity_test_create_bundle() and entity_test_delete_bundle() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by EntityTestHelper::createBundle() and EntityTestHelper::deleteBundle().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.2.0', 'drupal_requirements_severity', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'maxSeverityFromRequirements'),
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_create_bundle', 'Drupal\entity_test\EntityTestHelper', 'createBundle'),
        new FunctionToStaticConfiguration('11.2.0', 'entity_test_delete_bundle', 'Drupal\entity_test\EntityTestHelper', 'deleteBundle'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3489415
    // views_field_default_views_data() and _views_field_get_entity_type_storage() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by views.field_data_provider service methods.
    // https://www.drupal.org/node/3489411
    // views_entity_field_label() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by entity_field.manager::getFieldLabels().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.2.0', 'views_field_default_views_data', 'views.field_data_provider', 'defaultFieldImplementation'),
        new FunctionToServiceConfiguration('11.2.0', '_views_field_get_entity_type_storage', 'views.field_data_provider', 'getSqlStorageForField'),
        new FunctionToServiceConfiguration('11.2.0', 'views_entity_field_label', 'entity_field.manager', 'getFieldLabels'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3575841
    // REQUIREMENT_INFO/OK/WARNING/ERROR global constants deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by RequirementSeverity enum cases.
    // https://www.drupal.org/node/3488133
    // LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('REQUIREMENT_INFO', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Info', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_OK', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'OK', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_WARNING', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Warning', '11.2.0'),
        new ConstantToClassConfiguration('REQUIREMENT_ERROR', 'Drupal\Core\Extension\Requirement\RequirementSeverity', 'Error', '11.2.0'),
        new ConstantToClassConfiguration('LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN', 'Drupal', 'TRANSLATION_DEFAULT_SERVER_PATTERN', '11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3473440
    // https://www.drupal.org/node/3474692 (change record)
    // TwigNodeTrans 6th $tag constructor argument deprecated in twig/twig 3.12, removed in drupal:11.2.0.
    // Drop the argument.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTwigNodeTransTagArgumentRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3442810
    // https://www.drupal.org/node/3494472 (change record)
    // Number::alphadecimalToInt(null/'') deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Both arguments always produced 0; replaced with literal 0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceAlphadecimalToIntNullRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3512254
    // https://www.drupal.org/node/3515272 (change record)
    // #type 'fieldgroup' deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by 'fieldset'.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceFieldgroupToFieldsetRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3525077
    // https://www.drupal.org/node/3488338 (change record)
    // PDO::FETCH_* constants deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Database\Statement\FetchAs enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplacePdoFetchConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3574901
    // DateTimeRangeConstantsInterface::BOTH/START_DATE/END_DATE deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by DateTimeRangeDisplayOptions enum cases (->value).
    // datetime_type_field_views_data_helper() deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service('datetime.views_helper')->buildViewsData().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceDateTimeRangeConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3494172
    // file_get_content_headers($file) deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by $file->getDownloadHeaders().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(\DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector::class, [
        new \DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration('11.2.0', 'file_get_content_headers', 'getDownloadHeaders'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3518527
    // https://www.drupal.org/node/3518914 (change record)
    // $_SESSION['key'] = $value deprecated in drupal:11.2.0.
    // Replaced by \Drupal::request()->getSession()->set('key', $value).
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceSessionWritesWithRequestSessionRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3447794
    // https://www.drupal.org/node/3509245 (change record)
    // editor_load($format_id) deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by entityTypeManager()->getStorage('editor')->load($format_id).
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceEditorLoadRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3571065
    // $entity->original magic property deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Read access replaced by getOriginal(); write access replaced by setOriginal($value).
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceEntityOriginalPropertyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.2.0'),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3495943
    // #[StopProceduralHookScan] attribute renamed to #[ProceduralHookScanStop] in drupal:11.2.0.
    $ruleSince(RenameStopProceduralHookScanRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3511123
    // https://www.drupal.org/node/3511149 (change record)
    // CacheTagChecksumCount and CacheTagIsValidCount deprecated in drupal:11.2.0, removed in drupal:12.0.0. No replacement.
    $ruleSince(RemoveCacheTagChecksumAssertionsRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3506931
    // https://www.drupal.org/node/3511287 (change record)
    // Connection::createConnectionOptionsFromUrl() $root parameter deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Pass NULL explicitly instead of the root path argument.
    $ruleSince(RemoveRootFromCreateConnectionOptionsFromUrlRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3410939
    // SystemManager::REQUIREMENT_* deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Extension\Requirement\RequirementSeverity enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_OK',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'OK',
            '11.2.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_WARNING',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Warning',
            '11.2.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_ERROR',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Error',
            '11.2.0',
        ),
    ], 'drupal/core', '>=11.2.0');

    // https://www.drupal.org/node/3520946
    // https://www.drupal.org/node/3522240 (change record)
    // ViewsBlockBase::setConfigurationValue('items_per_page', 'none') deprecated in drupal:11.2.0,
    // removed in drupal:12.0.0. Replaced by NULL, which is the canonical value for inheriting
    // the items-per-page setting from the view.
    $ruleSince(ViewsBlockItemsPerPageNoneToNullRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3448457
    // https://www.drupal.org/node/3452144 (change record)
    // EntityFormMode::create() with 'description' => '' deprecated in drupal:11.2.0,
    // removed in drupal:12.0.0. Replaced by NULL, which is the canonical "no description" value
    // for entity display modes.
    $ruleSince(EntityFormModeEmptyDescriptionToNullRector::class, '>=11.2.0');

    // ---------------------------------------------------------------------
    // Drupal 11.2 (breaking)
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3009349
    // https://www.drupal.org/node/3306373 (change record)
    // The source_module constructor parameter was removed from
    // Drupal\migrate\Attribute\MigrateSource in drupal:11.2.0. Passing
    // #[MigrateSource(source_module: '...')] raises an "Unknown named
    // parameter" error at plugin discovery on 11.2.0+. This rule strips the
    // source_module named argument from #[MigrateSource] usages.
    //
    // Non-BC: an Attribute is not an Expr → Expr transformation, so it cannot
    // be BC-wrapped, and the argument is mutually exclusive across minors —
    // keeping it fatals on 11.2.0+, removing it drops the requirement metadata
    // DrupalSqlBase uses to enforce the source module for D6/D7 migrations on
    // older Drupal. For DrupalSqlBase plugins the value must be re-declared via
    // the @MigrateSource annotation or the migration YAML after removal; that
    // is a manual follow-up this rule does not automate. Apply only after
    // dropping support for Drupal minors that predate 11.2.0.
    //
    // TODO PHPSTAN_MESSAGES RemoveSourceModuleFromMigrateSourceAttributeRector:
    //   none. source_module was hard-removed (not @deprecated), so phpstan-
    //   deprecation-rules has no deprecation to report. Verified against a
    //   Drupal 11 test env: phpstan instead emits the hard error "Unknown
    //   parameter $source_module in call to Drupal\migrate\Attribute\
    //   MigrateSource constructor." (argument.unknownParameter, not a
    //   deprecation). Intentionally no coverage message.
    $ruleSince(RemoveSourceModuleFromMigrateSourceAttributeRector::class, '>=11.2.0');

    // https://www.drupal.org/node/3488572
    // https://www.drupal.org/node/3488580 (change record)
    // Drupal\Core\Entity\Query\Sql\pgsql\* deprecated in drupal:11.2.0,
    // removed in drupal:12.0.0. Moved to Drupal\pgsql\EntityQuery\*, which
    // does not exist on any Drupal 10.x branch.
    //
    // (3472008 / jsonapi ResourceResponseValidator: intentionally NOT included.
    // The "replacement" is a class inside core/modules/jsonapi/tests/modules/ —
    // a core test module that only resolves when explicitly enabled. Rewriting
    // every reference to the production FQCN into the test-module FQCN would
    // fatal on D10 AND on any production D11 site that does not enable that
    // test module. There is no version of "breaking" that makes this rename
    // safe at runtime, so the rule is not shipped.)
    //
    // https://www.drupal.org/node/3498915
    // https://www.drupal.org/node/3498916 (change record)
    // Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity[Deriver]
    // deprecated in drupal:11.2.0, removed in drupal:12.0.0. Moved to
    // Drupal\migrate\Plugin\migrate\source\*, which does not exist on any
    // Drupal 10.x branch.
    //
    // https://www.drupal.org/node/3258581
    // https://www.drupal.org/node/3439256 (change record)
    // Drupal\content_translation\Plugin\migrate\source\I18nQueryTrait
    // deprecated in drupal:11.2.0, removed in drupal:12.0.0. Moved to
    // Drupal\migrate_drupal\Plugin\migrate\source\I18nQueryTrait, which does
    // not exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\Core\Entity\Query\Sql\pgsql\QueryFactory' => 'Drupal\pgsql\EntityQuery\QueryFactory',
        'Drupal\Core\Entity\Query\Sql\pgsql\Condition' => 'Drupal\pgsql\EntityQuery\Condition',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntity' => 'Drupal\migrate\Plugin\migrate\source\ContentEntity',
        'Drupal\migrate_drupal\Plugin\migrate\source\ContentEntityDeriver' => 'Drupal\migrate\Plugin\migrate\source\ContentEntityDeriver',
        'Drupal\content_translation\Plugin\migrate\source\I18nQueryTrait' => 'Drupal\migrate_drupal\Plugin\migrate\source\I18nQueryTrait',
    ], 'drupal/core', '>=11.2.0');

    // ---------------------------------------------------------------------
    // Drupal 11.3
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3543035
    // https://www.drupal.org/node/3551729 (change record)
    // CommentManagerInterface::getCountNewComments() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\history\HistoryManager::getCountNewComments().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceCommentManagerGetCountNewCommentsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3536431
    // https://www.drupal.org/node/3536432 (change record)
    // ModuleHandler::loadAllIncludes() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by an explicit foreach over getModuleList() + loadInclude().
    $ruleSince(LoadAllIncludesRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3396062
    // https://www.drupal.org/node/3519187 (change record)
    // NodeStorage::revisionIds() and userRevisionIds() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by equivalent entity queries.
    $ruleSince(NodeStorageDeprecatedMethodsRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3533083
    // node_mass_update() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeBulkUpdate::process().
    // https://www.drupal.org/node/3547356
    // twig_render_template() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(TwigThemeEngine::class)->renderTemplate().
    // twig_extension() is handled by ReplaceTwigExtensionRector below.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process', true),
        new FunctionToServiceConfiguration('11.3.0', 'twig_render_template', 'Drupal\Core\Template\TwigThemeEngine', 'renderTemplate'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3504125
    // The template_preprocess_*() functions deprecated in drupal:11.3.0,
    // removed in drupal:12.0.0. Initial template_preprocess functions are now
    // registered directly in hook_theme(); each is replaced by the equivalent
    // *Preprocess service method.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_layout', 'Drupal\layout_discovery\Hook\LayoutDiscoveryThemeHooks', 'preprocessLayout', true),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_table', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessTable'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_tablesort_indicator', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessTablesortIndicator'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_item_list', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessItemList'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_region', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessRegion'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_maintenance_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessMaintenancePage'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_maintenance_task_list', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessMaintenanceTaskList'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_install_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessInstallPage'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_image', 'Drupal\Core\Theme\ImagePreprocess', 'preprocessImage'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_breadcrumb', 'Drupal\Core\Breadcrumb\BreadcrumbPreprocess', 'preprocessBreadcrumb'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_pager', 'Drupal\Core\Pager\PagerPreprocess', 'preprocessPager'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_field', 'Drupal\Core\Field\FieldPreprocess', 'preprocessField'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_field_multiple_value_form', 'Drupal\Core\Field\FieldPreprocess', 'preprocessFieldMultipleValueForm'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_menu_local_task', 'Drupal\Core\Menu\MenuPreprocess', 'preprocessMenuLocalTask'),
        new FunctionToServiceConfiguration('11.3.0', 'template_preprocess_menu_local_action', 'Drupal\Core\Menu\MenuPreprocess', 'preprocessMenuLocalAction'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/1685492
    // twig_extension() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by the '.html.twig' string literal.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceTwigExtensionRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceNodeModuleProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3535528
    // block_content_add_body_field() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // The body field is now added via config.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('block_content_add_body_field'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/2010202
    // comment_uri($comment) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $comment->permalink().
    // https://www.drupal.org/node/3531945
    // node_type_get_description($node_type) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $node_type->getDescription().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(\DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector::class, [
        new \DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration('11.3.0', 'comment_uri', 'permalink'),
        new \DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration('11.3.0', 'node_type_get_description', 'getDescription'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3038908
    // https://www.drupal.org/node/3038909 (change record)
    // node_access_view_all_nodes() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by entityTypeManager()->getAccessControlHandler('node')->checkAllGrants().
    // drupal_static_reset('node_access_view_all_nodes') replaced by node.view_all_nodes_memory_cache->deleteAll().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceNodeAccessViewAllNodesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3574424 (digest issue)
    // https://www.drupal.org/node/3548329 (change record)
    // responsive_image_* functions deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service(ResponsiveImageBuilder::class)->method() calls.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_build_source_attributes', 'Drupal\responsive_image\ResponsiveImageBuilder', 'buildSourceAttributes', true),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_image_dimensions', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageDimensions', true),
        new FunctionToServiceConfiguration('11.3.0', 'responsive_image_get_mime_type', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getMimeType', true),
        new FunctionToServiceConfiguration('11.3.0', '_responsive_image_image_style_url', 'Drupal\responsive_image\ResponsiveImageBuilder', 'getImageStyleUrl', true),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3489266
    // https://www.drupal.org/node/3516778 (change record)
    // node_add_body_field() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by $this->createBodyField() from BodyFieldCreationTrait.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceNodeAddBodyFieldRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3513856
    // https://www.drupal.org/node/3513877 (change record)
    // UserSession::$name property read deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by getAccountName().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceUserSessionNamePropertyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3534092
    // file_system_settings_submit() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\file\Hook\FileHooks::settingsSubmit().
    // https://www.drupal.org/node/3534089
    // https://www.drupal.org/node/3534091 (change record)
    // file_managed_file_submit() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\file\Element\ManagedFile::submit().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.3.0', 'file_system_settings_submit', 'Drupal\file\Hook\FileHooks', 'settingsSubmit'),
        new FunctionToStaticConfiguration('11.3.0', 'file_managed_file_submit', 'Drupal\file\Element\ManagedFile', 'submit'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3534089
    // https://www.drupal.org/node/3534091 (change record)
    // 'file_managed_file_submit' string callback deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by [\Drupal\file\Element\ManagedFile::class, 'submit'] array callable.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FileManagedFileSubmitRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3495601
    // JSONAPI_FILTER_AMONG_* global constants deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\jsonapi\JsonApiFilter::AMONG_* class constants.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ALL', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ALL', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_PUBLISHED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_PUBLISHED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_ENABLED', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_ENABLED', '11.3.0'),
        new ConstantToClassConfiguration('JSONAPI_FILTER_AMONG_OWN', 'Drupal\jsonapi\JsonApiFilter', 'AMONG_OWN', '11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3538277
    // https://www.drupal.org/node/3538666 (change record)
    // DRUPAL_DISABLED/OPTIONAL/REQUIRED constants (and integers 0/1/2) in setPreviewMode()
    // deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by NodePreviewMode enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceNodeSetPreviewModeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3530461
    // https://www.drupal.org/node/3530869 (change record)
    // FileSystemInterface::basename() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by PHP native basename().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FileSystemBasenameToNativeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3526515
    // https://www.drupal.org/node/3529500 (change record)
    // Error::currentErrorHandler() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by PHP built-in get_error_handler().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ErrorCurrentErrorHandlerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3573896
    // theme_get_setting() and _system_default_theme_features() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by ThemeSettingsProvider service.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceThemeGetSettingRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3522513
    // https://www.drupal.org/node/3511287 (change record)
    // Database::convertDbUrlToConnectionInfo($url, $root, ...) deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // The $root parameter is obsolete; remove it (shift any $include_test_drivers arg left).
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveRootFromConvertDbUrlRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3525388
    // https://www.drupal.org/node/3525389 (change record)
    // RendererInterface::addCacheableDependency() deprecated in drupal:11.3.0,
    // throws in drupal:13.0.0, when the dependency cannot implement
    // CacheableDependencyInterface. Removes calls whose dependency argument is
    // provably a primitive/array (bool, int, float, string, null, array).
    $ruleSince(RemoveRendererAddCacheableDependencyNonObjectRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3571054
    // https://www.drupal.org/node/3440844 (change record)
    // OpenDialogCommand / OpenOffCanvasDialogCommand $dialog_options['dialogClass']
    // deprecated in drupal:11.3.0, removed in drupal:12.0.0. Replaced by
    // $dialog_options['classes']['ui-dialog']. The replacement form has existed in
    // core since 10.3.x, so the transformed output is safe on every drupal-rector–
    // supported Drupal minor (D10.3+); no BC wrapper needed.
    $ruleSince(ReplaceDialogClassOptionRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3535439
    // https://www.drupal.org/node/3542527 (change record)
    // $variables['page'] in taxonomy term preprocess hooks deprecated in
    // drupal:11.3.0, removed in drupal:13.0.0. Reads replaced with
    // $variables['view_mode'] === 'full'; the comparison is pure PHP and works
    // on every Drupal version, so no BC wrapper is needed.
    $ruleSince(TaxonomyTermPageVariableToViewModeRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3538660
    // https://www.drupal.org/node/3538678 (change record)
    // Passing an int to CommentTestBase::setCommentPreview() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by Drupal\comment\CommentPreviewMode enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceCommentPreviewConstantsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3529274
    // https://www.drupal.org/node/3530638 (change record)
    // ViewsConfigUpdater registered as a service in drupal:11.3.0. Replace
    // \Drupal::classResolver(ViewsConfigUpdater::class) with
    // \Drupal::service(ViewsConfigUpdater::class) so state set via
    // setDeprecationsEnabled(FALSE) persists across hook invocations.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ViewsConfigUpdaterClassResolverToServiceRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3544308
    // https://www.drupal.org/node/3544527 (change record)
    // The $module_handler and $entity_type_manager arguments to
    // CommentLinkBuilder::__construct() deprecated in drupal:11.3.0, removed in
    // drupal:12.0.0. The 5-argument constructor call is rewritten to the new
    // 3-argument form ($current_user, $comment_manager, $string_translation);
    // BC-wrapped because the 3-argument signature only exists on Drupal >= 11.3.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(CommentLinkBuilderConstructorRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ], 'drupal/core', '>=11.3.0');

    // ---------------------------------------------------------------------
    // Drupal 11.3 (breaking)
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3490846
    // https://www.drupal.org/node/3549685 (change record)
    // hook_requirements_alter() deprecated in drupal:11.3.0, removed in
    // drupal:13.0.0. Renames procedural {module}_requirements_alter() to
    // {module}_runtime_requirements_alter(). The runtime hook is only invoked on
    // Drupal minors where it exists, so on older Drupal the renamed function is
    // never called (a silent no-op) — a non-BC rewrite. It cannot be BC-wrapped
    // (a function declaration is not an Expr → Expr transformation), hence the
    // breaking set. Apply only after dropping support for Drupal minors that
    // predate hook_runtime_requirements_alter().
    $ruleSince(HookRequirementsAlterRenameRector::class, '>=11.3.0');

    // https://www.drupal.org/node/1019966
    // https://www.drupal.org/node/2690393 (change record)
    // hook_ranking() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Renames the OOP hook attribute #[Hook('ranking')] to
    // #[Hook('node_search_ranking')]. The node_search_ranking hook is only
    // invoked on Drupal minors where it exists, so on older Drupal the renamed
    // attribute is never invoked (a silent no-op) — a non-BC rewrite. It cannot
    // be BC-wrapped (an Attribute is not an Expr → Expr transformation), hence
    // the breaking set. Apply only after dropping support for Drupal minors that
    // predate hook_node_search_ranking().
    //
    // TODO PHPSTAN_MESSAGES RenameHookRankingRector: none. The hook_ranking()
    //   deprecation is a @deprecated docblock on the node.api.php documentation
    //   function; the hook system resolves hook names as runtime strings, so
    //   phpstan-deprecation-rules has nothing to attach to the 'ranking' string
    //   literal inside #[Hook('ranking')]. Verified against contrib
    //   download_statistics 1.0.x (the rector transforms it correctly, but
    //   PHPStan emits no deprecation for the attribute). Runtime-only
    //   deprecation — intentionally no coverage message.
    $ruleSince(RenameHookRankingRector::class, '>=11.3.0');

    // https://www.drupal.org/node/3551446
    // https://www.drupal.org/node/3551450 (change record)
    // workspaces.association service / WorkspaceAssociationInterface renamed
    // in drupal:11.3.0. Replacement WorkspaceTracker[Interface] introduced in
    // 11.3.0; does not exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\workspaces\WorkspaceAssociationInterface' => 'Drupal\workspaces\WorkspaceTrackerInterface',
        'Drupal\workspaces\WorkspaceAssociation' => 'Drupal\workspaces\WorkspaceTracker',
    ], 'drupal/core', '>=11.3.0');

    // https://www.drupal.org/node/3571874
    // https://www.drupal.org/node/3527501 (change record)
    // Drupal\block_content\Access\* class aliases deprecated in drupal:11.3.0,
    // removed in drupal:12.0.0. The canonical Drupal\Core\Access\* homes were
    // added in 11.3.0; on every Drupal 10.x branch the only copy lives at
    // Drupal\block_content\Access\*, so rewriting to the Core\Access\* path
    // will fatal on D10.
    //
    // TODO PHPSTAN_MESSAGES RenameClassRector: capture against a Drupal 11.3.x
    //   test env (aliases are already gone from 11.4-dev, so live capture is
    //   not possible here). Expected shape from phpstan-deprecation-rules is
    //   either "Class MyBlock implements deprecated interface
    //   Drupal\block_content\Access\..." (for `implements`) or "Extending
    //   deprecated class Drupal\block_content\Access\..." (for `extends`).
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\block_content\Access\AccessGroupAnd' => 'Drupal\Core\Access\AccessGroupAnd',
        'Drupal\block_content\Access\DependentAccessInterface' => 'Drupal\Core\Access\DependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessInterface' => 'Drupal\Core\Access\RefinableDependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessTrait' => 'Drupal\Core\Access\RefinableDependentAccessTrait',
    ], 'drupal/core', '>=11.3.0');

    // ---------------------------------------------------------------------
    // Drupal 11.4
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3566424
    // https://www.drupal.org/node/3566982 (change record)
    // Views::pluginManager() and Views::handlerManager() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service('plugin.manager.views.*') or views.plugin_managers service.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ViewsPluginHandlerManagerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3578055
    // node_access_grants() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeGrantsHelper::nodeAccessGrants().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'node_access_grants', 'Drupal\node\NodeGrantsHelper', 'nodeAccessGrants', true),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3533299
    // https://www.drupal.org/node/3534610 (change record)
    // node_access_rebuild() and node_access_needs_rebuild() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeAccessRebuild service.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(NodeAccessRebuildFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/2536594
    // https://www.drupal.org/node/3035368 (change record)
    // filter_formats(), filter_get_roles_by_format(), filter_get_formats_by_role(),
    // filter_default_format(), and filter_fallback_format() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\filter\FilterFormatRepositoryInterface service.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FilterFormatFunctionsToServiceRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3568124
    // https://www.drupal.org/node/3566774 (change record)
    // media_filter_format_edit_form_validate() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\media\Hook\MediaHooks::formatEditFormValidate().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(MediaFilterFormatEditFormValidateRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3226806
    // https://www.drupal.org/node/3566774 (change record)
    // _filter_autop(), _filter_html_escape(), and _filter_html_image_secure_process()
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by plugin.manager.filter createInstance() chain.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(DeprecatedFilterFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3570851
    // SessionManager::delete() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Session\UserSessionRepositoryInterface::deleteAll().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceSessionManagerDeleteRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3550054
    // CommentItemInterface::FORM_BELOW and FORM_SEPARATE_PAGE deprecated in 11.4.0,
    // removed in 13.0.0. Replaced by FormLocation enum cases.
    // https://www.drupal.org/node/3547352
    // CommentItemInterface::HIDDEN/CLOSED/OPEN and CommentInterface::ANONYMOUS_*
    // deprecated in 11.4.0, removed in 13.0.0. Replaced by CommentingStatus and
    // AnonymousContact enum cases.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_BELOW',
            'Drupal\comment\FormLocation',
            'Below',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_SEPARATE_PAGE',
            'Drupal\comment\FormLocation',
            'SeparatePage',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'HIDDEN',
            'Drupal\comment\CommentingStatus',
            'Hidden',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'CLOSED',
            'Drupal\comment\CommentingStatus',
            'Closed',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'OPEN',
            'Drupal\comment\CommentingStatus',
            'Open',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAYNOT_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Forbidden',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAY_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Allowed',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MUST_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Required',
            '11.4.0',
        ),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3574727
    // language_configuration_element_submit() deprecated in 11.4.0, removed in 13.0.0.
    // Replaced by LanguageConfiguration::submit().
    // language_process_language_select() deprecated in 11.4.0, removed in 12.0.0.
    // Replaced by LanguageHooks::processLanguageSelect() via the service container.
    // https://www.drupal.org/node/3566792
    // ckeditor5_filter_format_edit_form_submit() and _update_ckeditor5_html_filter()
    // deprecated in 11.4.0, removed in 12.0.0. Replaced by Ckeditor5Hooks service.
    // https://www.drupal.org/node/3560398
    // _dblog_get_message_types() and dblog_filters() deprecated in 11.4.0,
    // removed in 13.0.0. Replaced by DbLogFilters service.
    // https://www.drupal.org/node/3566888
    // contact_user_profile_form_submit() and contact_form_user_admin_settings_submit()
    // deprecated in 11.4.0, removed in 12.0.0. Replaced by ContactFormHooks service.
    // https://www.drupal.org/node/3548571
    // content_translation_* functions deprecated in 11.4.0, removed in 12.0.0/13.0.0.
    // https://www.drupal.org/node/3572339
    // locale_translation_batch_update_build() and locale_translation_batch_fetch_build()
    // deprecated in 11.4.0, removed in 13.0.0. Replaced by LocaleFetch service.
    // https://www.drupal.org/node/3569328
    // locale.translation.inc functions deprecated in 11.4.0, removed in 13.0.0.
    // https://www.drupal.org/node/3571400
    // menu_ui.module procedural functions deprecated in 11.4.0, removed in 12.0.0/13.0.0.
    // https://www.drupal.org/node/3568387
    // text_summary() deprecated in 11.4.0, removed in 13.0.0. Replaced by TextSummary service.
    // https://www.drupal.org/node/3582107
    // user_form_process_password_confirm() deprecated in 11.4.0, removed in 13.0.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'language_process_language_select', 'Drupal\language\Hook\LanguageHooks', 'processLanguageSelect'),
        new FunctionToServiceConfiguration('11.4.0', 'ckeditor5_filter_format_edit_form_submit', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'filterFormatEditFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_update_ckeditor5_html_filter', 'Drupal\ckeditor5\Hook\Ckeditor5Hooks', 'updateCkeditor5HtmlFilter'),
        new FunctionToServiceConfiguration('11.4.0', '_dblog_get_message_types', 'Drupal\dblog\DbLogFilters', 'getMessageTypes'),
        new FunctionToServiceConfiguration('11.4.0', 'dblog_filters', 'Drupal\dblog\DbLogFilters', 'filters'),
        new FunctionToServiceConfiguration('11.4.0', 'contact_user_profile_form_submit', 'Drupal\contact\Hook\ContactFormHooks', 'profileFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'contact_form_user_admin_settings_submit', 'Drupal\contact\Hook\ContactFormHooks', 'userAdminSettingsSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_translate_access', 'content_translation.manager', 'access'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_enable_widget', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'getWidget'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_process', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementProcess'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_validate', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'content_translation_language_configuration_element_submit', 'Drupal\content_translation\ContentTranslationEnableTranslationPerBundle', 'configElementSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_content_translation_install_field_storage_definitions', 'Drupal\content_translation\Hook\ContentTranslationHooks', 'installFieldStorageDefinitions'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_update_build', 'Drupal\locale\LocaleFetch', 'batchUpdateBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_batch_fetch_build', 'Drupal\locale\LocaleFetch', 'batchFetchBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_get_projects', 'locale.project', 'getProjects'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_clear_cache_projects', 'locale.project', 'resetCache'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_load_sources', 'Drupal\locale\LocaleSource', 'loadSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_sources', 'Drupal\locale\LocaleSource', 'buildSources'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_check_file', 'Drupal\locale\LocaleSource', 'sourceCheckFile'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_source_build', 'Drupal\locale\LocaleSource', 'sourceBuild'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_build_server_pattern', 'Drupal\locale\LocaleSource', 'buildServerPattern'),
        new FunctionToServiceConfiguration('11.4.0', '_menu_ui_node_save', 'Drupal\menu_ui\MenuUiUtility', 'menuUiNodeSave'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_get_menu_link_defaults', 'Drupal\menu_ui\MenuUiUtility', 'getMenuLinkDefaults'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_node_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'nodeBuilder'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_form_submit', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_validate', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormValidate'),
        new FunctionToServiceConfiguration('11.4.0', 'menu_ui_form_node_type_form_builder', 'Drupal\menu_ui\Hook\MenuUiHooks', 'formNodeTypeFormBuilder'),
        new FunctionToServiceConfiguration('11.4.0', 'text_summary', 'Drupal\text\TextSummary', 'generate'),
        new FunctionToServiceConfiguration('11.4.0', 'user_form_process_password_confirm', 'Drupal\user\Hook\UserThemeHooks', 'processPasswordConfirm'),
        // https://www.drupal.org/node/3567163
        // https://www.drupal.org/node/3566774 (change record)
        // field_ui_form_manage_field_form_submit() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
        // Replaced by \Drupal\field_ui\Hook\FieldUiHooks::manageFieldFormSubmit().
        new FunctionToServiceConfiguration('11.4.0', 'field_ui_form_manage_field_form_submit', 'Drupal\field_ui\Hook\FieldUiHooks', 'manageFieldFormSubmit', true),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3035340
    // views_ui_contextual_links_suppress*() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // These are no-ops and can be removed.
    // https://www.drupal.org/node/3566768
    // https://www.drupal.org/node/3566774 (change record)
    // automated_cron_settings_submit() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Config saving is now handled automatically via #config_target on the interval element.
    // https://www.drupal.org/node/3566783
    // block_theme_initialize() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Logic moved to protected BlockHooks::themeInitialize(); external callers must drop the call.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionCallRemovalRector::class, [
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_push'),
        new FunctionCallRemovalConfiguration('views_ui_contextual_links_suppress_pop'),
        new FunctionCallRemovalConfiguration('automated_cron_settings_submit'),
        new FunctionCallRemovalConfiguration('block_theme_initialize'),
        new FunctionCallRemovalConfiguration('syslog_facility_list'),
        new FunctionCallRemovalConfiguration('syslog_logging_settings_submit'),
        new FunctionCallRemovalConfiguration('taxonomy_build_node_index'),
        new FunctionCallRemovalConfiguration('taxonomy_delete_node_index'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/2667040
    // https://www.drupal.org/node/3575062 (change record)
    // EntityTypeInterface::setUriCallback() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Use link templates or a route provider instead.
    $ruleSince(RemoveSetUriCallbackRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3498026
    // https://www.drupal.org/node/3579527 (change record)
    // RecipeRunner::installModule() deprecated in drupal:11.4.0. Use installModules() with an array.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceRecipeRunnerInstallModuleRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3184242
    // https://www.drupal.org/node/3526344 (change record)
    // system.performance css.gzip and js.gzip config keys deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by css.compress and js.compress.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceSystemPerformanceGzipKeyRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3564937
    // https://www.drupal.org/node/3564958 (change record)
    // CachePluginBase::getRowCacheKeys() and getRowId() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Remove array items whose value is one of these calls.
    $ruleSince(RemoveViewsRowCacheKeysRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3576556
    // https://www.drupal.org/node/3576855 (change record)
    // CachePluginBase::cacheExpire() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Subclass overrides are dead code; remove them.
    $ruleSince(RemoveCacheExpireOverrideRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3347842
    // https://www.drupal.org/node/3348180 (change record)
    // trustData() deprecated in drupal:11.4.0, removed in drupal:13.0.0. Remove from fluent chains.
    // Config::save($has_trusted_data) boolean arg deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveTrustDataCallRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveConfigSaveTrustedDataArgRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3093118
    // https://www.drupal.org/node/3554139 (change record)
    // LinkWidget::validateTitleElement() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Validation is now handled by LinkTitleRequiredConstraint on the LinkItem field type.
    $ruleSince(RemoveLinkWidgetValidateTitleElementRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3566768
    // https://www.drupal.org/node/3566774 (change record)
    // $form['#submit'][] = 'automated_cron_settings_submit' deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Config saving is now handled automatically via #config_target on the interval element.
    $ruleSince(RemoveAutomatedCronSubmitHandlerRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3572243
    // https://www.drupal.org/node/3572594 (change record)
    // views_view_is_enabled(), views_view_is_disabled(), views_enable_view(),
    // views_disable_view(), views_get_view_result() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by OO equivalents on the view object or Views::getViewResult().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceViewsProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3557461
    // https://www.drupal.org/node/3557464 (change record)
    // EntityTypeInterface::getOriginalClass() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by getDecoratedClasses()[0].
    $rectorConfig->ruleWithConfigurationComposerVersionBound(GetOriginalClassToGetDecoratedClassesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3566801
    // https://www.drupal.org/node/3566814 (change record)
    // getEntityTypeIdKeyType() === 'integer', entityTypeSupportsComments(), and hasIntegerId($entityType)
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by EntityTypeInterface::hasIntegerId() called on the entity type object.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(UseEntityTypeHasIntegerIdRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3568144
    // editor_filter_xss() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service('element.editor')->filterXss().
    // https://www.drupal.org/node/3570917
    // editor_image_upload_settings_form() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service(EditorImageUploadSettings::class)->getForm().
    // https://www.drupal.org/node/2907780
    // field_purge_batch() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal::service(FieldPurger::class)->purgeBatch().
    // https://www.drupal.org/node/3566774
    // _media_library_media_type_form_submit() and _media_library_views_form_media_library_after_build()
    // deprecated in drupal:11.4.0, removed in drupal:12.0.0. Replaced by MediaLibraryHooks service.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'editor_filter_xss', 'element.editor', 'filterXss'),
        new FunctionToServiceConfiguration('11.4.0', 'editor_image_upload_settings_form', 'Drupal\editor\EditorImageUploadSettings', 'getForm'),
        new FunctionToServiceConfiguration('11.4.0', 'field_purge_batch', 'Drupal\Core\Field\FieldPurger', 'purgeBatch'),
        new FunctionToServiceConfiguration('11.4.0', '_media_library_media_type_form_submit', 'Drupal\media_library\Hook\MediaLibraryHooks', 'mediaTypeFormSubmit'),
        new FunctionToServiceConfiguration('11.4.0', '_media_library_views_form_media_library_after_build', 'Drupal\media_library\Hook\MediaLibraryHooks', 'viewsFormAfterBuild'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3566774
    // _media_library_configure_form_display() and _media_library_configure_view_display()
    // deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by MediaLibraryDisplayManager static methods.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_form_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureFormDisplay'),
        new FunctionToStaticConfiguration('11.4.0', '_media_library_configure_view_display', 'Drupal\media_library\MediaLibraryDisplayManager', 'configureViewDisplay'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3574727
    // language_configuration_element_submit() deprecated in 11.4.0, removed in 13.0.0.
    // Replaced by LanguageConfiguration::submit().
    // https://www.drupal.org/node/3566774
    // views_ui/admin.inc static trait functions deprecated in 11.4.0, removed in 13.0.0.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.4.0', 'language_configuration_element_submit', 'Drupal\language\Element\LanguageConfiguration', 'submit'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_form_button_was_clicked', 'Drupal\views\ViewsFormHelperTrait', 'formButtonWasClicked'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_limited_validation', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addLimitedValidation'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_add_ajax_wrapper', 'Drupal\views\ViewsFormAjaxHelperTrait', 'addAjaxWrapper'),
        new FunctionToStaticConfiguration('11.4.0', 'views_ui_nojs_submit', 'Drupal\views\ViewsFormAjaxHelperTrait', 'noJsSubmit'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3568087
    // contextual_links_to_id() and contextual_id_to_links() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by ContextualLinksSerializer service.
    // https://www.drupal.org/node/3567618
    // image_path_flush() and image_style_options() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by ImageDerivativeUtilities service.
    // https://www.drupal.org/node/3577675
    // locale_translate_get_interface_translation_files() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by LocaleFileManager::getInterfaceTranslationFiles().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', '_contextual_links_to_id', 'Drupal\contextual\ContextualLinksSerializer', 'linksToId'),
        new FunctionToServiceConfiguration('11.4.0', '_contextual_id_to_links', 'Drupal\contextual\ContextualLinksSerializer', 'idToLinks'),
        new FunctionToServiceConfiguration('11.4.0', 'image_path_flush', 'Drupal\image\ImageDerivativeUtilities', 'pathFlush'),
        new FunctionToServiceConfiguration('11.4.0', 'image_style_options', 'Drupal\image\ImageDerivativeUtilities', 'styleOptions'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translate_get_interface_translation_files', 'Drupal\locale\File\LocaleFileManager', 'getInterfaceTranslationFiles'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_http_check', 'Drupal\locale\File\LocaleFileManager', 'checkRemoteFileStatus'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translate_delete_translation_files', 'Drupal\locale\File\LocaleFileManager', 'deleteTranslationFiles'),
        new FunctionToServiceConfiguration('11.4.0', 'locale_translation_download_source', 'Drupal\locale\File\LocaleFileManager', 'downloadTranslationSource'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/2571679
    // https://www.drupal.org/node/3382344 (change record)
    // views_add_contextual_links() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\views\ContextualLinksHelper::addLinks() service call.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.4.0', 'views_add_contextual_links', 'Drupal\views\ContextualLinksHelper', 'addLinks', true),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3567619
    // IMAGE_DERIVATIVE_TOKEN deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\image\ImageStyleInterface::TOKEN.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ConstantToClassConstantRector::class, [
        new ConstantToClassConfiguration('IMAGE_DERIVATIVE_TOKEN', 'Drupal\image\ImageStyleInterface', 'TOKEN', '11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/2940605
    // EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by literal 20.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceEntityReferenceRecursiveLimitRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3015812
    // system_region_list() and system_default_region() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by Theme object methods via \Drupal::service('theme_handler')->getTheme().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(SystemRegionFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/455724
    // https://www.drupal.org/node/3588040 (change record)
    // check_markup() deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by a processed_text render array.
    $ruleSince(CheckMarkupToProcessedTextRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3571172
    // https://www.drupal.org/node/3566774 (change record)
    // system_sort_themes() string callback deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by an inline static closure.
    $ruleSince(SystemSortThemesRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3037031
    // locale_translation_flush_projects(), locale_translation_build_projects(), locale_translation_check_projects(),
    // and locale_translation_check_projects_local() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by LocaleProjectRepository and LocaleProjectChecker service methods.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(LocaleCompareIncToServiceRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3581303
    // https://www.drupal.org/node/3589759 (change record)
    // The locale batch procedural callbacks in locale.batch.inc, locale.bulk.inc,
    // and locale.compare.inc deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by methods on the LocaleFetch, LocaleImportBatch, LocaleConfigBatch,
    // and LocaleProjectChecker services. BC-wrapped because the LocaleImportBatch
    // and LocaleConfigBatch services (and the new methods on the existing services)
    // do not exist on Drupal < 11.4. locale_config_batch_build() and
    // locale_translation_batch_status_build() are intentionally not rewritten:
    // their signature/behavior changed and they require manual migration.
    // PHPStan deprecation messages captured in the rector's PHPSTAN_MESSAGES const.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceLocaleBatchProceduralFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3335756
    // https://www.drupal.org/node/3349345 (change record)
    // KernelTestBase::installSchema('system', 'sequences') deprecated in
    // drupal:10.2.0 and removed in drupal:12.0.0. The sequences table no
    // longer exists in core; the call throws a LogicException on D12 and
    // must be removed (or have 'sequences' stripped from its array form).
    $ruleSince(RemoveInstallSchemaSystemSequencesRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3548957
    // https://www.drupal.org/node/3548961 (change record)
    // Drupal\Component\Utility\ToStringTrait deprecated in drupal:11.4.0 and
    // removed in drupal:13.0.0. The trait was a PHP 7 workaround for fatal
    // errors thrown inside __toString(); on PHP 8+ exceptions propagate
    // normally. Inline `public function __toString(): string { return (string)
    // $this->render(); }` replaces it. Pure PHP — runs on every supported
    // Drupal version, no BC wrapper.
    $ruleSince(RemoveDrupalToStringTraitRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3559481
    // https://www.drupal.org/node/3562304 (change record)
    // ImageToolkitOperationBase::__construct() $toolkit argument deprecated in drupal:11.4.0,
    // removed in drupal:13.0.0. Plugin manager now injects via setToolkit() for autowiring.
    $ruleSince(RemoveToolkitArgFromImageToolkitOperationConstructorRector::class, '>=11.4.0');

    // https://www.drupal.org/node/2258355
    // https://www.drupal.org/node/3261271 (change record)
    // hide() and show() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by direct $element['#printed'] = TRUE/FALSE assignment.
    $ruleSince(ReplaceHideShowWithPrintedRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3526250
    // Integer values for #access render array key deprecated in drupal:11.4.0,
    // removed in drupal:13.0.0. Replaced by boolean or AccessResultInterface.
    // Only integer literals are rewritten (1 → true, 0 → false); variables and
    // typed expressions are left for manual review.
    $ruleSince(ReplaceNonBoolAccessRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3589047
    // https://www.drupal.org/node/3574112 (change record)
    // DrupalTestCaseTrait::getDrupalRoot() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by direct access to the $this->root property on Drupal base test classes.
    $ruleSince(GetDrupalRootToRootPropertyRector::class, '>=11.4.0');

    // https://www.drupal.org/node/1452100
    // https://www.drupal.org/node/3573884 (change record)
    // drupal_static_reset('file_get_file_references') and
    // drupal_static_reset('file_get_file_references:field_columns') deprecated in
    // drupal:11.4.0, removed in drupal:13.0.0. Replaced by
    // \Drupal::service('cache.memory')->invalidateTags(['file_references']).
    // BC-wrapped: the 'file_references' cache tag does not exist before 11.4.0, so
    // the new call would be a silent no-op there; the DeprecationHelper wrapper
    // keeps the original drupal_static_reset() on older versions.
    // TODO PHPSTAN_MESSAGES ReplaceDrupalStaticResetFileReferencesRector:
    //   No PHPStan deprecation is emitted. The deprecated thing is the static-cache
    //   key string passed to drupal_static_reset(); the deprecation lives in a
    //   runtime @trigger_error inside file_get_file_references(), not on a
    //   @deprecated PHP symbol PHPStan analyses. Nothing to match.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceDrupalStaticResetFileReferencesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3550268
    // https://www.drupal.org/node/3545276 (change record)
    // ExpectDeprecationTrait deprecated in drupal:11.4.0, removed in drupal:12.0.0.
    // Replaced by PHPUnit 11+ expectUserDeprecationMessage() / expectUserDeprecationMessageMatches().
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceExpectDeprecationRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3571593
    // https://www.drupal.org/node/3571594 (change record)
    // locale.settings:translation.path config key deprecated in drupal:11.4.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\Core\Site\Settings::get('locale_translation_path', 'public://translations').
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceLocaleTranslationPathConfigRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3582118
    // PhpUnitCompatibilityTrait is DELETED FROM CORE in Drupal 12 — any test
    // class still composing the trait fatal-errors at autoload on D12.
    //
    // GATED TO 12.0.0 INTENTIONALLY. The trait still exists (and may still
    // hold shim methods) on Drupal 10. On Drupal 11 it is an empty no-op but
    // removing the composition is harmless. On Drupal 12 the trait class is
    // gone and the composition MUST be removed. Because the trait composition
    // cannot be BC-wrapped (it's a structural Class_ change, not an Expr →
    // Expr rewrite), the rector is deliberately OFF by default and only fires
    // when the consumer sets DrupalRectorSettings::setDrupalVersion('12.0.0')
    // or higher. This prevents accidentally stripping a still-functional
    // trait composition from a D10/D11-only codebase.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemovePhpUnitCompatibilityTraitRector::class, [
        new DrupalIntroducedVersionConfiguration('12.0.0'),
    ], 'drupal/core', '>=12.0.0');

    // https://www.drupal.org/project/drupal/issues/3445240 (meta: add #[RunTestsInSeparateProcesses])
    // PHPUnit 10+ runs each test class in a separate process when this attribute is
    // present. Introduced at 11.4.0 to limit blast radius — additive only (no
    // annotation to remove). Added to Kernel and Functional (Browser) tests, not
    // pure Unit tests. NOTE: this changes test *execution* on PHPUnit 10/11, not
    // only on Drupal 12.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(PhpUnitAddRunTestsInSeparateProcessesAttributeRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3561135
    // https://www.drupal.org/node/3554746 (change record)
    // Passing an options array to the UploadedFileConstraint constructor is
    // deprecated in drupal:11.4.0, removed in drupal:12.0.0. Replaced by named
    // constructor arguments. BC-wrapped because the named-argument constructor
    // was introduced alongside the deprecation, so the new form would fatal on
    // Drupal < 11.4.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(UploadedFileConstraintArrayOptionsToNamedArgsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3554447
    // https://www.drupal.org/node/3554585 (change record)
    // The '#item_attributes' property of the image_formatter and
    // responsive_image_formatter theme hooks deprecated in drupal:11.4.0,
    // removed in drupal:12.0.0. Replaced by '#attributes'. BC-wrapped because
    // the '#attributes' variable was only added to these hooks in 11.4.0, so a
    // plain rename silently drops the attributes on Drupal < 11.4.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceItemAttributesWithAttributesRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3311365
    // https://www.drupal.org/node/3324751 (change record)
    // RouteBuilder::__construct() $module_handler and $controller_resolver
    // arguments deprecated in drupal:11.4.0, removed in drupal:12.0.0. YAML
    // route discovery moved to the new YamlRouteDiscovery service; the 6-arg
    // constructor form is rewritten to the new 4-arg form.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RemoveRouteBuilderDeprecatedArgsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // https://www.drupal.org/node/3555936
    // user_load_by_name() and user_load_by_mail() deprecated in drupal:11.4.0,
    // removed in drupal:13.0.0. Replaced by an entity storage loadByProperties()
    // lookup, normalised with array_values(...)[0] ?? FALSE to preserve the
    // original single-object-or-FALSE return contract. Pure entity-API + PHP —
    // runs on every supported Drupal version, so no BC wrapper.
    $ruleSince(UserLoadByNameAndMailRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3581056
    // https://www.drupal.org/node/3581062 (change record)
    // user_pass_rehash(), user_pass_reset_url() and user_cancel_url()
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0. Replaced by the
    // new \Drupal\user\OneTimeAuthentication service (generateHmac(),
    // generateOneTimeLoginUrl(), generateCancelConfirmUrl()). BC-wrapped
    // because the service does not exist on Drupal < 11.4. The two URL methods
    // return a Url object, so the rewrite chains ->toString().
    // TODO PHPSTAN_MESSAGES ReplaceUserOneTimeAuthFunctionsRector:
    //   Not yet captured. The functions are @deprecated PHP symbols (so PHPStan
    //   will emit "Call to deprecated function user_pass_rehash()" etc.), but the
    //   deprecation only landed in core commit a38f1d17 (committed 2026-06-08) and
    //   is not in the installed 11.4-dev test core yet. Capture the three messages
    //   once the test core is updated past that commit.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(ReplaceUserOneTimeAuthFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ], 'drupal/core', '>=11.4.0');

    // ---------------------------------------------------------------------
    // Drupal 11.4 (breaking)
    // ---------------------------------------------------------------------

    // https://www.drupal.org/node/3560075
    // https://www.drupal.org/node/3572239 (change record)
    // Drupal\menu_link_content\Plugin\migrate\process\LinkOptions and LinkUri
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0. The replacement
    // classes were ADDED to Drupal\migrate\Plugin\migrate\process in the same
    // commit as the deprecation (drupal-core 4b7913fb19a, on 11.x only) — they
    // do not exist on any Drupal 10.x branch. Running this rule against code
    // that still needs to work on Drupal 10 will produce a "class not found"
    // fatal there.
    //
    // The Drupal 11.4 search plugin renames — HelpSearch → SearchHelpSearch
    // (https://www.drupal.org/node/3581109) and NodeSearch → SearchNode
    // (https://www.drupal.org/node/3587564) — used to live here and were
    // deliberately dropped; see https://github.com/palantirnet/drupal-rector/issues/414.
    // Both replacements live in new core sub-modules (search_help, search_node)
    // that the rewritten module must then depend on, and RenameClassRector can
    // rewrite the `use` / `extends` / `::class` reference but cannot add the
    // `dependencies:` line to the module's info.yml — so its output is
    // incomplete by construction. Core also restored both old classes as
    // deprecated stubs on 11.4.x (drupal-core af7dd6efc0, #3608912), so the
    // rename is no longer needed to avoid a missing-class fatal.
    //
    // https://www.drupal.org/node/3589630
    // https://www.drupal.org/node/3589636 (change record)
    // Drupal\node\Controller\NodeViewController deprecated in drupal:11.4.0,
    // removed in drupal:13.0.0. Use
    // Drupal\Core\Entity\Controller\EntityViewController instead.
    //
    // Unlike the migrate renames above, the replacement class exists on every
    // supported minor (EntityViewController has been the parent of
    // NodeViewController since Drupal 8), so the rewrite never fatals on a
    // missing symbol. It is in the breaking set because the rename is
    // *behaviorally* breaking, identically on every minor: rewriting
    // `extends NodeViewController` to `extends EntityViewController` drops the
    // node-specific overrides (4-service create(), the currentUser /
    // entityRepository properties, the title() callback, the node view()
    // signature). A subclass relying on them can throw an ArgumentCountError
    // (inherited 2-arg create() vs a 4-arg constructor) or call an undefined
    // title(). These need manual review, so the rule is opt-in.
    //
    // PHPSTAN_MESSAGES RenameClassRector: NodeViewController is annotated
    //   `@deprecated in drupal:11.4.0` at the class level, so
    //   phpstan-deprecation-rules emits "Class ... extends deprecated class
    //   Drupal\node\Controller\NodeViewController: ..." for subclasses and
    //   "Instantiation of deprecated class ..." for direct `new` calls. The
    //   instantiation message is carried by
    //   ReplaceNodeViewControllerRector::PHPSTAN_MESSAGES.
    $rectorConfig->ruleWithConfigurationComposerVersionBound(RenameClassRector::class, [
        'Drupal\menu_link_content\Plugin\migrate\process\LinkOptions' => 'Drupal\migrate\Plugin\migrate\process\LinkOptions',
        'Drupal\menu_link_content\Plugin\migrate\process\LinkUri' => 'Drupal\migrate\Plugin\migrate\process\LinkUri',
        'Drupal\node\Controller\NodeViewController' => 'Drupal\Core\Entity\Controller\EntityViewController',
    ], 'drupal/core', '>=11.4.0');

    // ReplaceNodeViewControllerRector additionally trims the extra
    // $current_user / $entity_repository constructor arguments from
    // `new NodeViewController(...)`, which RenameClassRector cannot do. It
    // matches both class names, so the trim is order-independent w.r.t. the
    // RenameClassRector pass above.
    $ruleSince(ReplaceNodeViewControllerRector::class, '>=11.4.0');

    // https://www.drupal.org/node/2987159
    // https://www.drupal.org/node/3521459 (change record)
    // block_content_query_entity_reference_alter() — the hook that
    // automatically filtered non-reusable blocks out of every entity reference
    // selection targeting block_content — is deprecated, removed in
    // drupal:12.0.0. Entity reference selection plugins that extend
    // Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection
    // directly must now extend
    // Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection
    // instead, which performs the reusable-block filtering itself.
    //
    // BlockContentSelection was ADDED to core alongside the deprecation
    // (drupal-core 7f9570dba9, on the 11.4-dev branch — a new class ships in a
    // minor, so 11.4.0, even though the runtime deprecation message text reads
    // "11.3.0"). It does not exist on any earlier minor, so reparenting a
    // subclass onto it produces a "class not found" fatal on Drupal < 11.4. A
    // `class X extends Y` declaration is a structural node, not an Expr → Expr
    // rewrite, so it cannot be BC-wrapped.
    //
    // PHPSTAN_MESSAGES BlockContentSelectionExtendsRector: none. The deprecation
    // is a runtime @trigger_error raised inside the query_entity_reference_alter
    // hook when the auto-filtering happens, not a static @deprecated annotation
    // on any symbol the plugin references, so phpstan-deprecation-rules /
    // upgrade_status cannot flag a `class X extends DefaultSelection`
    // declaration. There is no static message to match.
    $ruleSince(BlockContentSelectionExtendsRector::class, '>=11.4.0');

    // https://www.drupal.org/node/3505370
    // https://www.drupal.org/node/3567879 (change record)
    // The $long parameter of FilterInterface::tips() / FilterBase::tips() was
    // deprecated in drupal:11.4.0 and is removed in drupal:12.0.0. The "filter
    // tips" long-format page goes away with it.
    //
    // RemoveFilterTipsLongParamRector strips $long from a plugin's tips()
    // override (and drops the second argument from _filter_tips() calls). This
    // is NOT BC: FilterInterface and FilterBase still declare tips($long = FALSE)
    // on every Drupal minor below 11.4, and PHP rejects an override that *drops*
    // a parameter the parent declares with a fatal at class-declaration time:
    //   "Declaration of MyFilter::tips() must be compatible with
    //    Drupal\filter\Plugin\FilterInterface::tips($long = false)".
    // (The reverse — a parent that dropped $long with a subclass that still
    // declares the optional param — is fine, which is why core can deprecate it
    // ahead of removal, but a rector that rewrites the *subclass* cannot.)
    //
    // So this rule may only be applied once the consumer's minimum supported
    // Drupal is >= 11.4. It is opt-in here and must NOT block Drupal 12
    // compatibility: on Drupal 12 the parameter is gone from the parent too, so
    // the un-rewritten subclass keeps an extra optional param — phpstan will
    // grumble but it runs. Contrib that still supports Drupal < 11.4 should not
    // run this rule (it would fatal those sites); see the rejected token_filter
    // change at https://www.drupal.org/project/token_filter/issues/3603786.
    //
    // PHPSTAN_MESSAGES RemoveFilterTipsLongParamRector: the $long parameter is
    //   annotated `@deprecated in drupal:11.4.0` (a parameter-level deprecation),
    //   for which phpstan-deprecation-rules emits no discrete message — there is
    //   no static @deprecated symbol the override references. The runtime
    //   @trigger_error fires inside core's tips() handling, not at the call site.
    //   Nothing for upgrade_status to match against.
    $ruleSince(RemoveFilterTipsLongParamRector::class, '>=11.4.0');

    // ---------------------------------------------------------------------
    // Drupal 12.0
    // ---------------------------------------------------------------------

    // Symfony 8 (Drupal 12) added native `: void` return types to
    // ConstraintValidatorInterface::validate()/initialize() and `mixed $value`
    // to validate() (the latter since Symfony 7). Add them to implementers.
    // Backward compatible on all supported Drupal versions, so no version gate.
    // https://git.drupalcode.org/project/redirect/-/merge_requests/200
    $ruleSince(AddSymfonyConstraintValidatorTypeDeclarationsRector::class, '>=12.0.0');
};
