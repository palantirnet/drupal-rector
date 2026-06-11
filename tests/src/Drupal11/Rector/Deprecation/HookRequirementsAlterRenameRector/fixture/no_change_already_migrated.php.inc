<?php

// Already-migrated runtime hook and the manually-added update hook both end in
// _requirements_alter; the rule must leave them untouched (idempotent).
function mymodule_runtime_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}

function mymodule_update_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}
