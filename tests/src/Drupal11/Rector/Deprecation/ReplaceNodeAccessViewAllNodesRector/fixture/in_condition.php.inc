<?php

// Result used in an if-condition
if (node_access_view_all_nodes()) {
    do_something();
}

// Result used in a condition with an explicit account
if (node_access_view_all_nodes($account)) {
    do_something_else();
}

?>
-----
<?php

// Result used in an if-condition
if (\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants(\Drupal::currentUser())) {
    do_something();
}

// Result used in a condition with an explicit account
if (\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants($account)) {
    do_something_else();
}

?>
