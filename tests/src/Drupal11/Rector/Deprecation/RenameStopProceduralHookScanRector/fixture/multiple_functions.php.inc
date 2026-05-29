<?php

use Drupal\Core\Hook\Attribute\StopProceduralHookScan;

function mymodule_does_something(): void {}

#[StopProceduralHookScan]
function mymodule_helper(): void {}

function mymodule_other_hook(): void {}

?>
-----
<?php

use Drupal\Core\Hook\Attribute\ProceduralHookScanStop;

function mymodule_does_something(): void {}

#[ProceduralHookScanStop]
function mymodule_helper(): void {}

function mymodule_other_hook(): void {}

?>
