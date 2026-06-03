<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function untypedReceiver($renderer): void
{
    $build = [];
    $renderer->addCacheableDependency($build, false);
}
?>
-----
<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function untypedReceiver($renderer): void
{
    $build = [];
    $renderer->addCacheableDependency($build, false);
}
?>
