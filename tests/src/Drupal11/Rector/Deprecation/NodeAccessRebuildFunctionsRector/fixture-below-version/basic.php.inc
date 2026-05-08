<?php

node_access_rebuild();
node_access_rebuild(TRUE);
$needs = node_access_needs_rebuild();
node_access_needs_rebuild(TRUE);
node_access_needs_rebuild(FALSE);
?>
-----
<?php

node_access_rebuild();
node_access_rebuild(TRUE);
$needs = node_access_needs_rebuild();
node_access_needs_rebuild(TRUE);
node_access_needs_rebuild(FALSE);
?>
