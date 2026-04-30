<?php

$entity->trustData()->save();
$other->someMethod();

?>
-----
<?php

$entity->save();
$other->someMethod();

?>
