<?php

// Standalone $config->get() without a recognisable chained loader — not transformed.
// The rule deliberately matches only chained config-accessor expressions to keep the
// rewrite obviously correct; users with the standalone pattern can refactor manually.
/** @var \Drupal\Core\Config\ImmutableConfig $config */
$path = $config->get('translation.path');
?>
-----
<?php

// Standalone $config->get() without a recognisable chained loader — not transformed.
// The rule deliberately matches only chained config-accessor expressions to keep the
// rewrite obviously correct; users with the standalone pattern can refactor manually.
/** @var \Drupal\Core\Config\ImmutableConfig $config */
$path = $config->get('translation.path');
?>
