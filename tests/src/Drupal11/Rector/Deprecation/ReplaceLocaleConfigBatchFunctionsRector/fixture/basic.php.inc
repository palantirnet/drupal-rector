<?php

locale_config_batch_set_config_langcodes($context);
locale_config_batch_refresh_name($names, $langcodes, $context);
other_function($context);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_default_config_langcodes($context), fn() => locale_config_batch_set_config_langcodes($context));
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => locale_config_batch_update_config_translations($names, $langcodes, $context), fn() => locale_config_batch_refresh_name($names, $langcodes, $context));
other_function($context);

?>
