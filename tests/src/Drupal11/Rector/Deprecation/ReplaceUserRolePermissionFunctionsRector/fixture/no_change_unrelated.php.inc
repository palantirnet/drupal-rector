<?php

// Unrelated global functions that merely look similar must be left untouched.
mymodule_user_role_grant_permissions('anonymous', ['access content']);
user_role_grant_permission('anonymous', 'access content');

// Argument shapes the deprecated functions never had, and call forms whose
// positional order cannot be resolved statically, are skipped rather than
// rewritten into something that would not compile.
user_role_grant_permissions();
user_role_grant_permissions('anonymous', ['access content'], TRUE);
user_role_revoke_permissions(rid: 'anonymous', permissions: ['access content']);
user_role_change_permissions(...$args);
?>
-----
<?php

// Unrelated global functions that merely look similar must be left untouched.
mymodule_user_role_grant_permissions('anonymous', ['access content']);
user_role_grant_permission('anonymous', 'access content');

// Argument shapes the deprecated functions never had, and call forms whose
// positional order cannot be resolved statically, are skipped rather than
// rewritten into something that would not compile.
user_role_grant_permissions();
user_role_grant_permissions('anonymous', ['access content'], TRUE);
user_role_revoke_permissions(rid: 'anonymous', permissions: ['access content']);
user_role_change_permissions(...$args);
?>
