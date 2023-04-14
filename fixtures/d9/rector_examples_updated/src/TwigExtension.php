<?php

namespace Drupal\twig_tweak;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Block\TitleBlockPluginInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Markup;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\media\Plugin\media\Source\OEmbedInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Twig extension with some useful functions and filters.
 *
 * Dependencies are not injected for performance reason.
 */
class TwigExtension extends \Twig_Extension {

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('drupal_config', [$this, 'drupalConfig']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return [
            new \Twig_SimpleFilter('token_replace', [$this, 'tokenReplaceFilter']),
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
