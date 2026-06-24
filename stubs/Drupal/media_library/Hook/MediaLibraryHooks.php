<?php

declare(strict_types=1);

namespace Drupal\media_library\Hook;

if (class_exists(\Drupal\media_library\Hook\MediaLibraryHooks::class)) {
    return;
}

class MediaLibraryHooks
{
    public function mediaTypeFormSubmit(&$form, $formState): void {}

    public function viewsFormAfterBuild($form, $formState) {}
}
