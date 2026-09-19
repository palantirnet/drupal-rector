<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceUserRolePermissionFunctionsRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
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

    // https://www.drupal.org/node/2012976
    // https://www.drupal.org/node/3379194 (change record)
    //
    // user_login_finalize() and user_logout() deprecated in drupal:11.5.0,
    // removed in drupal:13.0.0. Both function bodies moved verbatim into the
    // new \Drupal\user\LoginFinalizer and \Drupal\user\LogoutFinalizer
    // services (drupal-core accb9caa1fc), so the mapping is 1-to-1:
    // finalizeLogin(UserInterface $user): void takes the single argument
    // user_login_finalize(UserInterface $account): void took, and
    // finalizeLogout(): void takes none.
    //
    // BC-wrapped: both services arrive with the deprecation itself, so the
    // rewritten call fatals on Drupal < 11.5.
    //
    // The only shape the rewrite cannot carry is a named argument — the
    // parameter is $user on the service and was $account on the function. No
    // contrib project calls either function with a named or extra argument
    // (0 hits across the contrib index), so no guard is warranted here.
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.5.0', 'user_login_finalize', 'Drupal\user\LoginFinalizer', 'finalizeLogin', true),
        new FunctionToServiceConfiguration('11.5.0', 'user_logout', 'Drupal\user\LogoutFinalizer', 'finalizeLogout', true),
    ]);
};
