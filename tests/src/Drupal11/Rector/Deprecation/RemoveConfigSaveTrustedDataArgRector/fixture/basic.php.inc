<?php

$config->save(TRUE);
$config->save(FALSE);
$config->save();
$other->save(TRUE);

?>
-----
<?php

$config->save();
$config->save();
$config->save();
$other->save();

?>
