<?php

$ext = twig_extension();
?>
-----
<?php

$ext = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => '.html.twig', fn() => twig_extension());
?>
