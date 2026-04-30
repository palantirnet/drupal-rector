<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3501136
    // template_preprocess_*() functions deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by ThemePreprocess and DatePreprocess service methods.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_time', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessTime'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_form', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeForm'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_datetime_wrapper', 'Drupal\Core\Datetime\DatePreprocess', 'preprocessDatetimeWrapper'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_links', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessLinks'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_container', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessContainer'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_html', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessHtml'),
        new FunctionToServiceConfiguration('11.2.0', 'template_preprocess_page', 'Drupal\Core\Theme\ThemePreprocess', 'preprocessPage'),
    ]);

    // https://www.drupal.org/node/3575841
    // SystemManager::REQUIREMENT_* deprecated in drupal:11.2.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\Extension\Requirement\RequirementSeverity enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_OK',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'OK',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_WARNING',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Warning',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_ERROR',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Error',
        ),
    ]);
};
