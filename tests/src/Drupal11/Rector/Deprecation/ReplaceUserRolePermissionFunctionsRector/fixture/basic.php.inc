<?php

user_role_grant_permissions('anonymous', ['access content']);
user_role_revoke_permissions('anonymous', ['access content']);
user_role_change_permissions('anonymous', ['access content' => TRUE]);
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree('anonymous')?->grantPermissions(['access content'])?->save(), fn() => user_role_grant_permissions('anonymous', ['access content']));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree('anonymous')?->revokePermissions(['access content'])?->save(), fn() => user_role_revoke_permissions('anonymous', ['access content']));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.5.0', fn() => \Drupal\user\Entity\Role::loadOverrideFree('anonymous')?->changePermissions(['access content' => TRUE])?->save(), fn() => user_role_change_permissions('anonymous', ['access content' => TRUE]));
?>
