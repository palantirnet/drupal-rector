<?php

// An implicitly nullable intersection type is already a fatal error on PHP 8.1,
// so only code that needs PHP 8.2 or newer has it. The rewrite, a DNF type,
// needs PHP 8.2 too.
function my_module_intersection_helper(\Countable&\Traversable $both = null) {
}

?>
-----
<?php

// An implicitly nullable intersection type is already a fatal error on PHP 8.1,
// so only code that needs PHP 8.2 or newer has it. The rewrite, a DNF type,
// needs PHP 8.2 too.
function my_module_intersection_helper((\Countable&\Traversable)|null $both = null) {
}

?>
