<?php

// Intentionally excluded: signature/behavior changed, requires manual migration.
locale_config_batch_build($options, $langcodes);
locale_translation_batch_status_build($projects, $langcodes);
// Unrelated functions with similar prefixes must not be touched.
locale_translation_check_projects($projects);
locale_translate_batch_something_else($context);
?>
-----
<?php

// Intentionally excluded: signature/behavior changed, requires manual migration.
locale_config_batch_build($options, $langcodes);
locale_translation_batch_status_build($projects, $langcodes);
// Unrelated functions with similar prefixes must not be touched.
locale_translation_check_projects($projects);
locale_translate_batch_something_else($context);
?>
