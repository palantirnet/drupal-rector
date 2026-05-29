<?php

use Drupal\Core\Hook\Attribute\StopProceduralHookScan;

#[StopProceduralHookScan]
function mymodule_helper(): void {}

?>
-----
<?php

use Drupal\Core\Hook\Attribute\ProceduralHookScanStop;

#[ProceduralHookScanStop]
function mymodule_helper(): void {}

?>
