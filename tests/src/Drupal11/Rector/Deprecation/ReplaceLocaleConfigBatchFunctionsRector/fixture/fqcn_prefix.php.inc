<?php

// FQCN-prefixed global function call (backslash-prefixed in a namespaced context).
\locale_config_batch_set_config_langcodes($context);
\locale_config_batch_refresh_name($names, $langcodes, $context);

?>
-----
<?php

// FQCN-prefixed global function call (backslash-prefixed in a namespaced context).
locale_config_batch_update_default_config_langcodes($context);
locale_config_batch_update_config_translations($names, $langcodes, $context);

?>
