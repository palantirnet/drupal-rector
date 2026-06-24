<?php

declare(strict_types=1);

namespace Drupal\content_translation;

if (class_exists(\Drupal\content_translation\ContentTranslationEnableTranslationPerBundle::class)) {
    return;
}

class ContentTranslationEnableTranslationPerBundle
{
    public function getWidget($entityTypeId, $bundle, &$form, $formState): array { return []; }

    public function configElementProcess($element, $formState, &$form): array { return []; }

    public function configElementValidate($element, $formState, &$form): void {}

    public function configElementSubmit($form, $formState): void {}
}
