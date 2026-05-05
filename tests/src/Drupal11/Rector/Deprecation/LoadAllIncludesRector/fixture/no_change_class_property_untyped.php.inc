<?php

// Untyped class property — PHPStan cannot infer ModuleHandlerInterface, so no change.
class SomeMissingAnnotationService {
    protected $moduleHandler;

    public function __construct($moduleHandler) {
        $this->moduleHandler = $moduleHandler;
    }

    public function loadAll(): void {
        $this->moduleHandler->loadAllIncludes('install');
    }
}
