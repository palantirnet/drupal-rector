<?php

use Drupal\Core\Template\TwigNodeTrans;

// 5-argument form — no $tag argument, must not be changed.
$node5 = new TwigNodeTrans($body, $plural, $count, $options, $lineno);

// 4-argument form — also must not be changed.
$node4 = new TwigNodeTrans($body, $plural, $count, $options);

?>
-----
<?php

use Drupal\Core\Template\TwigNodeTrans;

// 5-argument form — no $tag argument, must not be changed.
$node5 = new TwigNodeTrans($body, $plural, $count, $options, $lineno);

// 4-argument form — also must not be changed.
$node4 = new TwigNodeTrans($body, $plural, $count, $options);

?>
