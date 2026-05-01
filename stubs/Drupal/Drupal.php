<?php

if (class_exists(\Drupal::class)) {
    return;
}

class Drupal {
    const VERSION = '11.99.x-dev';

    public static function moduleHandler(): \Drupal\Core\Extension\ModuleHandlerInterface {}
}
