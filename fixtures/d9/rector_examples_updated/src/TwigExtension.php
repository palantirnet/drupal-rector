<?php

namespace Drupal\twig_tweak;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
/**
 * Twig extension with some useful functions and filters.
 */
class TwigExtension extends AbstractExtension {

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new TwigFunction('drupal_config', [$this, 'drupalConfig']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return [
            new TwigFilter('token_replace', [$this, 'tokenReplaceFilter']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'twig_tweak';
    }

    /**
     * Replaces all tokens in a given string with appropriate values.
     *
     * Example:
     * @code
     *   # Basic usage.
     *   {{ '<h1>[site:name]</h1><div>[site:slogan]</div>'|token_replace }}
     *
     *   # This is more suited to large markup (requires Twig >= 1.41).
     *   {% apply token_replace %}
     *     <h1>[site:name]</h1>
     *     <div>[site:slogan]</div>
     *   {% endapply %}
     * @endcode
     *
     * @param string $text
     *   An HTML string containing replaceable tokens.
     *
     * @return string
     *   The entered HTML text with tokens replaced.
     */
    public function tokenReplaceFilter($text) {
        return \Drupal::token()->replace($text);
    }

    /**
     * Retrieves data from a given configuration object.
     *
     * Example:
     * @code
     *   {{ drupal_config('system.site', 'name') }}
     * @endcode
     *
     * @param string $name
     *   The name of the configuration object to construct.
     * @param string $key
     *   A string that maps to a key within the configuration data.
     *
     * @return mixed
     *   The data that was requested.
     */
    public function drupalConfig($name, $key) {
        return \Drupal::config($name)->get($key);
    }

}
