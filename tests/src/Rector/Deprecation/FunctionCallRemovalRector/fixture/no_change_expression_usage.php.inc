<?php

// Return value used: not a standalone statement, so not removed.
$result = block_content_add_body_field($block_type_id, $label);

// Passed as argument: inner call is not a statement, not removed.
other_function(template_preprocess($variables, 'mytemplate'));

?>
-----
<?php

// Return value used: not a standalone statement, so not removed.
$result = block_content_add_body_field($block_type_id, $label);

// Passed as argument: inner call is not a statement, not removed.
other_function(template_preprocess($variables, 'mytemplate'));

?>
