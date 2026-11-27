<?php

namespace Drupal\hookconvertrector_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
/**
 * Hook implementations for hookconvertrector_theme.
 */
class HookconvertrectorThemeHooks
{
    /**
     * Implements hook_preprocess_page().
     */
    #[Hook('preprocess_page')]
    public function preprocessPage(array &$variables)
    {
        $variables['site_slogan_visible'] = TRUE;
    }
    /**
     * Implements hook_preprocess_html().
     */
    #[Hook('preprocess_html')]
    public function preprocessHtml(array &$variables)
    {
        $variables['attributes']['class'][] = 'hookconvertrector-theme';
    }
    /**
     * Implements hook_theme_suggestions_page_alter().
     */
    #[Hook('theme_suggestions_page_alter')]
    public function themeSuggestionsPageAlter(array &$suggestions, array $variables)
    {
        $suggestions[] = 'page__hookconvertrector_theme';
    }
}
