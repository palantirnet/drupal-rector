<?php

// String literals that happen to spell 'REQUEST_TIME' are not ConstFetch nodes
// and must not be changed.
$constant_name = 'REQUEST_TIME';
$message = "The REQUEST_TIME constant has been removed.";

?>
