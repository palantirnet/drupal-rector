<?php

use Drupal\Core\Template\TwigNodeTrans;

$node = new TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());
$other = new OtherClass($a, $b, $c, $d, $e, $f);

?>
-----
<?php

use Drupal\Core\Template\TwigNodeTrans;

$node = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => new TwigNodeTrans($body, $plural, $count, $options, $lineno), fn() => new TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag()));
$other = new OtherClass($a, $b, $c, $d, $e, $f);

?>
