<?php

use Drupal\system\SystemManager;

function example() {
    $requirements['check']['severity'] = SystemManager::REQUIREMENT_OK;
    $requirements['check']['severity'] = SystemManager::REQUIREMENT_WARNING;
    $requirements['check']['severity'] = SystemManager::REQUIREMENT_ERROR;
}
?>
-----
<?php

use Drupal\system\SystemManager;

function example() {
    $requirements['check']['severity'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::OK, fn() => SystemManager::REQUIREMENT_OK);
    $requirements['check']['severity'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::Warning, fn() => SystemManager::REQUIREMENT_WARNING);
    $requirements['check']['severity'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::Error, fn() => SystemManager::REQUIREMENT_ERROR);
}
?>
