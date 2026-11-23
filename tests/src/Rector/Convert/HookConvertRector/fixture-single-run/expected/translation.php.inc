<?php

namespace Drupal\singlerun\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
/**
 * Hook implementations for singlerun.
 */
class TranslationHooks
{
    use StringTranslationTrait;
    /**
     * Implements hook_help().
     */
    #[Hook('help')]
    public function help($route_name, $bytes)
    {
        return $this->t('Size: @size', [
            '@size' => \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.2.0', fn() => \Drupal\Core\StringTranslation\SizeMarkup::create($bytes), fn() => format_size($bytes)),
        ]);
    }
}
