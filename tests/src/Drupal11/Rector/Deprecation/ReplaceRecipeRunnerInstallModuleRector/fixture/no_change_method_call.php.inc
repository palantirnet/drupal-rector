<?php

// Method call (not a static call) is not transformed — rector only handles StaticCall nodes.
$runner->installModule($module, $storage, $context);

?>
-----
<?php

// Method call (not a static call) is not transformed — rector only handles StaticCall nodes.
$runner->installModule($module, $storage, $context);

?>
