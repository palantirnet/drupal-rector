<?php

// Attribute used with fully-qualified class name — no use statement.
#[\Drupal\Core\Hook\Attribute\StopProceduralHookScan]
function mymodule_helper(): void {}

?>
-----
<?php

// Attribute used with fully-qualified class name — no use statement.
#[ProceduralHookScanStop]
function mymodule_helper(): void {}

?>
