<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function untypedCallSite($aliasManager): void
{
    $aliasManager->setCacheKey('/some/path');
    $aliasManager->writeCache();
}
?>
-----
<?php

// Untyped receiver — the type guard rejects this to avoid false positives.
function untypedCallSite($aliasManager): void
{
    $aliasManager->setCacheKey('/some/path');
    $aliasManager->writeCache();
}
?>
