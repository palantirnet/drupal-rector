<?php

// $permissions defaults to [] in core's signature, so a one-argument call is
// legal and rewrites to an explicit empty array.
user_role_grant_permissions('anonymous');
?>
-----
<?php

// $permissions defaults to [] in core's signature, so a one-argument call is
// legal and rewrites to an explicit empty array.
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree('anonymous')?->grantPermissions([])?->save(), fn() => user_role_grant_permissions('anonymous'));
?>
