<?php

// Real-world contrib shape (e.g. acquia_cms_common, social_course): the role ID
// is a variable or a RoleInterface constant rather than a literal, and the
// permissions array is built up beforehand.
function mymodule_install(): void {
    $rid = 'editor';
    $permissions = ['access content', 'administer nodes'];
    user_role_grant_permissions($rid, $permissions);
    user_role_revoke_permissions(\Drupal\user\RoleInterface::ANONYMOUS_ID, $permissions);
}
?>
-----
<?php

// Real-world contrib shape (e.g. acquia_cms_common, social_course): the role ID
// is a variable or a RoleInterface constant rather than a literal, and the
// permissions array is built up beforehand.
function mymodule_install(): void {
    $rid = 'editor';
    $permissions = ['access content', 'administer nodes'];
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree($rid)?->grantPermissions($permissions)?->save(), fn() => user_role_grant_permissions($rid, $permissions));
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree(\Drupal\user\RoleInterface::ANONYMOUS_ID)?->revokePermissions($permissions)?->save(), fn() => user_role_revoke_permissions(\Drupal\user\RoleInterface::ANONYMOUS_ID, $permissions));
}
?>
