<?php

// No use statement — instantiated via fully-qualified class name.
$node = new \Drupal\Core\Template\TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());

?>
-----
<?php

// No use statement — instantiated via fully-qualified class name.
$node = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => new \Drupal\Core\Template\TwigNodeTrans($body, $plural, $count, $options, $lineno), fn() => new \Drupal\Core\Template\TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag()));

?>
