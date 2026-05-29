<?php

// $plugin has no type annotation, so PHPStan cannot confirm it is a PluginBase.
// Without that confirmation the rector must not change the call.
$result = $plugin->isConfigurable();
