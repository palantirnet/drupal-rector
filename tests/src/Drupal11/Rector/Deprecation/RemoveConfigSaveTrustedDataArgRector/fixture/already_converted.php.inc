<?php

// Code previously converted while the rector still emitted a DeprecationHelper
// wrapper must be left untouched: the deprecated save(TRUE) call lives in the
// `deprecatedCallable` arm and is skipped by isInBackwardsCompatibleCall().
/** @var \Drupal\Core\Config\Config $config */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $config->save(), fn() => $config->save(TRUE));

?>
