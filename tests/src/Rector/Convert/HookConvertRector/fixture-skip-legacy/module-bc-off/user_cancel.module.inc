<?php

/**
 * Implements hook_user_cancel().
 */
function skiplegacy_user_cancel($edit, $account, $method)
{
    $account->block();
}
-----
<?php
