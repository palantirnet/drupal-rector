<?php

/**
 * The hook_requirements_alter() API documentation function itself is not an
 * implementation and must never be renamed.
 */
function hook_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}
