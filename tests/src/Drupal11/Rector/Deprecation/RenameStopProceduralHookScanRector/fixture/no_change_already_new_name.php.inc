<?php

// Already uses the new name — must not be double-renamed.
use Drupal\Core\Hook\Attribute\ProceduralHookScanStop;

#[ProceduralHookScanStop]
function mymodule_helper(): void {}
