<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserRolePermissionFunctionsRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/2025089
    // https://www.drupal.org/node/3348027 (change record)
    //
    // user_role_grant_permissions(), user_role_revoke_permissions() and
    // user_role_change_permissions() deprecated in drupal:11.5.0, removed in
    // drupal:13.0.0. Replaced by the matching \Drupal\user\RoleInterface
    // methods on a role loaded with \Drupal\user\Entity\Role::loadOverrideFree().
    //
    // BC-wrapped: loadOverrideFree() is itself new in 11.5.0
    // (drupal-core e1f2b85d319, https://www.drupal.org/i/3620216), so the
    // rewritten chain fatals with "Call to undefined method" on Drupal < 11.5.
    //
    // PHPSTAN_MESSAGES live on the rector class itself
    // (ReplaceUserRolePermissionFunctionsRector::PHPSTAN_MESSAGES), captured
    // against drupal/core 11.x-dev via matomo, pwa and poll.
    $rectorConfig->ruleWithConfiguration(ReplaceUserRolePermissionFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.5.0'),
    ]);
};
