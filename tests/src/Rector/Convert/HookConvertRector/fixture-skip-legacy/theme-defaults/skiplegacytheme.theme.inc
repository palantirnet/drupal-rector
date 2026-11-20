<?php

/**
 * Implements hook_preprocess_page().
 */
function skiplegacytheme_preprocess_page(&$variables)
{
    $variables['skip'] = TRUE;
}
