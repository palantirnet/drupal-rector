<?php

// $plugin->isConfigurable() — receiver is not $this, so it must not be changed.
// Other classes (e.g. Action, CKEditor5PluginDefinition) have isConfigurable()
// with different semantics.
$result = $plugin->isConfigurable();
