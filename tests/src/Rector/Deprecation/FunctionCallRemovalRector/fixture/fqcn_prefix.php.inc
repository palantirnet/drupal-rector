<?php

\block_content_add_body_field($block_type_id, $label);
\template_preprocess($variables, 'mytemplate');
other_function();

?>
-----
<?php

other_function();

?>
