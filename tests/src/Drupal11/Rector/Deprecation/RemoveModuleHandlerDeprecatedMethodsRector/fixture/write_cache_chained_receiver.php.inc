<?php

// writeCache() called on an untyped chained receiver — not transformed
// because PHPStan cannot resolve the return type of getModuleHandler().
$builder->getModuleHandler()->writeCache();

?>
-----
<?php

// writeCache() called on an untyped chained receiver — not transformed
// because PHPStan cannot resolve the return type of getModuleHandler().
$builder->getModuleHandler()->writeCache();

?>
