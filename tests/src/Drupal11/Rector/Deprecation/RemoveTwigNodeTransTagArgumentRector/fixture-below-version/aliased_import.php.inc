<?php

use Drupal\Core\Template\TwigNodeTrans as NodeTrans;

// Aliased import — Rector resolves the alias to the FQCN before matching,
// so this form IS correctly transformed.
$node = new NodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());

?>
-----
<?php

use Drupal\Core\Template\TwigNodeTrans as NodeTrans;

// Aliased import — Rector resolves the alias to the FQCN before matching,
// so this form IS correctly transformed.
$node = new NodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());

?>
