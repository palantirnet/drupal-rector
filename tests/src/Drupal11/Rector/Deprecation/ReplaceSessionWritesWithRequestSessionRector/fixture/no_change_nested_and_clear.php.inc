<?php

// Nested write: $_SESSION['outer']['inner'] = $v — var is ArrayDimFetch,
// not Variable, so the guard fails. Known limitation.
$_SESSION['outer']['inner'] = $value;

// Bare clear: $_SESSION = [] — var is Variable without ArrayDimFetch. Not handled.
$_SESSION = [];

// unset() is a different node type (Unset_), not an Assign. Not handled.
unset($_SESSION['my_key']);

?>
-----
<?php

// Nested write: $_SESSION['outer']['inner'] = $v — var is ArrayDimFetch,
// not Variable, so the guard fails. Known limitation.
$_SESSION['outer']['inner'] = $value;

// Bare clear: $_SESSION = [] — var is Variable without ArrayDimFetch. Not handled.
$_SESSION = [];

// unset() is a different node type (Unset_), not an Assign. Not handled.
unset($_SESSION['my_key']);

?>
