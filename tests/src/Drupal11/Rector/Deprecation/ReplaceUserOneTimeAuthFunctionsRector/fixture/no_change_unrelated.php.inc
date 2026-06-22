<?php

// An unrelated global function with one of the same argument shapes must be
// left untouched — only the three deprecated user functions are rewritten.
user_pass_rehash_custom($account, $timestamp);
some_other_module_pass_reset_url($account, $options);
?>
-----
<?php

// An unrelated global function with one of the same argument shapes must be
// left untouched — only the three deprecated user functions are rewritten.
user_pass_rehash_custom($account, $timestamp);
some_other_module_pass_reset_url($account, $options);
?>
