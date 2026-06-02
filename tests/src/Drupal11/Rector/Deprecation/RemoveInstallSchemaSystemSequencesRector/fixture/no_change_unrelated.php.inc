<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function unrelatedCallSite($obj): void
{
    $obj->installSchema('system', 'sequences');
    $obj->installSchema('system', ['sequences']);
}
?>
-----
<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function unrelatedCallSite($obj): void
{
    $obj->installSchema('system', 'sequences');
    $obj->installSchema('system', ['sequences']);
}
?>
