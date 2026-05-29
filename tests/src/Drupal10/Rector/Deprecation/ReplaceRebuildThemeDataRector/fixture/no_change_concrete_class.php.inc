<?php

// Property typed as the concrete ThemeHandler class (not the interface).
// Without Drupal's full class hierarchy available in analysis, PHPStan cannot
// confirm ThemeHandlerInterface, so the call is not rewritten.
use Drupal\Core\Extension\ThemeHandler;

class SiteGuardianService {
    /**
     * @var \Drupal\Core\Extension\ThemeHandler
     */
    protected $themeHandler;

    public function __construct(ThemeHandler $themeHandler) {
        $this->themeHandler = $themeHandler;
    }

    public function rebuild(): array {
        return $this->themeHandler->rebuildThemeData();
    }
}
