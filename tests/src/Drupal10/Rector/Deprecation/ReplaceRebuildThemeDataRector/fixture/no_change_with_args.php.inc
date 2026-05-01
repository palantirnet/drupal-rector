<?php

// rebuildThemeData() with an argument — guarded by !empty($node->args), must not change.
/** @var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
$themes = $theme_handler->rebuildThemeData($extra);
