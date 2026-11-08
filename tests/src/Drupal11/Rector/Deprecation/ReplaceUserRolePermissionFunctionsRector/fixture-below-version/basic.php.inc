<?php

user_role_grant_permissions('anonymous', ['access content']);
user_role_revoke_permissions('anonymous', ['access content']);
user_role_change_permissions('anonymous', ['access content' => TRUE]);
?>
-----
<?php

user_role_grant_permissions('anonymous', ['access content']);
user_role_revoke_permissions('anonymous', ['access content']);
user_role_change_permissions('anonymous', ['access content' => TRUE]);
?>
