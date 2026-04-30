<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3550054
    // CommentItemInterface::FORM_BELOW and FORM_SEPARATE_PAGE deprecated in 11.4.0,
    // removed in 13.0.0. Replaced by FormLocation enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_BELOW',
            'Drupal\comment\FormLocation',
            'Below',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_SEPARATE_PAGE',
            'Drupal\comment\FormLocation',
            'SeparatePage',
        ),
    ]);

    // https://www.drupal.org/node/3574727
    // language_configuration_element_submit() deprecated in 11.4.0, removed in 13.0.0.
    // Replaced by LanguageConfiguration::submit().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration(
            '11.4.0',
            'language_configuration_element_submit',
            'Drupal\language\Element\LanguageConfiguration',
            'submit'
        ),
    ]);

    // https://www.drupal.org/node/3574727
    // language_process_language_select() deprecated in 11.4.0, removed in 12.0.0.
    // Replaced by LanguageHooks::processLanguageSelect() via the service container.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration(
            '11.4.0',
            'language_process_language_select',
            'Drupal\language\Hook\LanguageHooks',
            'processLanguageSelect'
        ),
    ]);
};
