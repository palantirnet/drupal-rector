<?php

declare(strict_types=1);

namespace Drupal\content_translation\Hook;

if (class_exists(\Drupal\content_translation\Hook\ContentTranslationHooks::class)) {
    return;
}

class ContentTranslationHooks
{
    public function installFieldStorageDefinitions($entityTypeId): void {}
}
