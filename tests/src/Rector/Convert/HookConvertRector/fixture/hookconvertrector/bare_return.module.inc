<?php

/**
 * Implements hook_page_attachments().
 */
function hookconvertrector_page_attachments(array &$page)
{
    if (condition()) {
        return;
    }
    doWork($page);
}
?>
-----
<?php

/**
 * Implements hook_page_attachments().
 */
#[\Drupal\Core\Hook\Attribute\LegacyHook]
function hookconvertrector_page_attachments(array &$page)
{
    \Drupal::service(\Drupal\hookconvertrector\Hook\BareReturnHooks::class)->pageAttachments($page);
}
?>
