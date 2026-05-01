<?php

// $linkWidget->validateTitleElement() — instance method call, not a static call.
// The rector only targets static calls, so this must NOT be removed.
$linkWidget->validateTitleElement($element, $form_state, $form);
