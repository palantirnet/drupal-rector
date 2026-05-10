<?php

// No type annotation — type guard prevents transformation.
$connection->createConnectionOptionsFromUrl($url, $root);

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $other */
$other->createConnectionOptionsFromUrl($url, $root);
?>
-----
<?php

// No type annotation — type guard prevents transformation.
$connection->createConnectionOptionsFromUrl($url, $root);

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $other */
$other->createConnectionOptionsFromUrl($url, $root);
?>
