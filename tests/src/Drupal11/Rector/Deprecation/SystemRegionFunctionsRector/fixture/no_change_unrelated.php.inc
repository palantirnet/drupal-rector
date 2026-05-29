<?php

// Unrelated function calls should not be transformed.
$regions = theme_get_registry();
$default = theme_default();
?>
-----
<?php

// Unrelated function calls should not be transformed.
$regions = theme_get_registry();
$default = theme_default();
?>
