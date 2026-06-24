<?php

if (class_exists(\Drupal::class)) {
    return;
}

class Drupal {
    const VERSION = '11.99.x-dev';

    public static function moduleHandler(): \Drupal\Core\Extension\ModuleHandlerInterface {}

    /**
     * @param $service
     *
     * @return \Drupal\Core\Cache\CacheBackendInterface
     */
    public static function service($service): \Drupal\Core\Cache\CacheBackendInterface {}
}
