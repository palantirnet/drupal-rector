<?php

declare(strict_types=1);

use DrupalRector\Drupal12\Rector\Deprecation\AddSymfonyConstraintValidatorTypeDeclarationsRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Symfony 8 (Drupal 12) added native `: void` return types to
    // ConstraintValidatorInterface::validate()/initialize() and `mixed $value`
    // to validate() (the latter since Symfony 7). Add them to implementers.
    // Backward compatible on all supported Drupal versions, so no version gate.
    // https://git.drupalcode.org/project/redirect/-/merge_requests/200
    $rectorConfig->rule(AddSymfonyConstraintValidatorTypeDeclarationsRector::class);
};
