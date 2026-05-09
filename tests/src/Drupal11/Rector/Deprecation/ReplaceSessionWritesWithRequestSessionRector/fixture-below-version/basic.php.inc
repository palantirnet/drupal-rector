<?php

$_SESSION['my_key'] = $value;
$_SESSION[$dynamic_key] = $other;
$other_var['key'] = 'untouched';

?>
-----
<?php

$_SESSION['my_key'] = $value;
$_SESSION[$dynamic_key] = $other;
$other_var['key'] = 'untouched';

?>
