<?php

namespace Drupal\hookconvertrector\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector.
 */
class BareReturnHooks
{
    /**
     * Implements hook_page_attachments().
     */
    #[Hook('page_attachments')]
    public function pageAttachments(array &$page)
    {
        if (condition()) {
            return;
        }
        doWork($page);
    }
}
