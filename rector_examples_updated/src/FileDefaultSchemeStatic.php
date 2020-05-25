<?php declare(strict_types=1);

namespace Drupal\rector_examples;

final class FileDefaultSchemeStatic
{

    /** @var string */
    private $scheme;

    public function __construct() {
        $this->scheme = \Drupal::config('system.file')->get('default_scheme');
    }

    public function getDefaultScheme():string {
        return $this->scheme;
    }
}
