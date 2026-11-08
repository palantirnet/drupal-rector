<?php

// Real-world contrib shape (matomo): the global function is called
// fully-qualified with a leading backslash, and the permissions array spans
// several lines.
\user_role_grant_permissions('authenticated', [
    'opt-in or out of matomo tracking',
]);
?>
-----
<?php

// Real-world contrib shape (matomo): the global function is called
// fully-qualified with a leading backslash, and the permissions array spans
// several lines.
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree('authenticated')?->grantPermissions([
    'opt-in or out of matomo tracking',
])?->save(), fn() => \user_role_grant_permissions('authenticated', [
    'opt-in or out of matomo tracking',
]));
?>
