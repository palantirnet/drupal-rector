<?php

// Unrelated function calls with similar names must be left untouched.
$account = user_load($uid);
$accounts = user_load_multiple($uids);
?>
