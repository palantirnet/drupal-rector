<?php

/**
 * Implements hook_preprocess_html().
 */
function skiplegacytheme_preprocess_html(&$variables)
{
    $variables['skip'] = FALSE;
}
-----
<?php
