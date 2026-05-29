<?php

// Static call using fully-qualified class name without a use statement.
\Drupal\link\Plugin\Field\FieldWidget\LinkWidget::validateTitleElement($element, $form_state, $form);
$x = 1;
?>
-----
<?php

$x = 1;
?>
