<?php

// REQUEST_TIME used as a function argument.
$date = date('Y-m-d H:i:s', REQUEST_TIME);

// REQUEST_TIME used as an array value.
$data = ['cutoff' => REQUEST_TIME];

// REQUEST_TIME in string concatenation (still a ConstFetch, so it IS transformed).
$label = 'Timestamp: ' . REQUEST_TIME;

?>
-----
<?php

// REQUEST_TIME used as a function argument.
$date = date('Y-m-d H:i:s', REQUEST_TIME);

// REQUEST_TIME used as an array value.
$data = ['cutoff' => REQUEST_TIME];

// REQUEST_TIME in string concatenation (still a ConstFetch, so it IS transformed).
$label = 'Timestamp: ' . REQUEST_TIME;

?>
