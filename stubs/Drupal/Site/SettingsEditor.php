<?php

namespace Drupal\Core\Site;

if (class_exists('Drupal\Core\Site\Settings')) {
    return;
}

final class SettingsEditor {

  private function __construct() {}

  public static function rewrite(string $settings_file, array $settings = []): void {

  }
}
