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
    $requirements['check']['severity'] = \Drupal\Core\Extension\Requirement\RequirementSeverity::OK;
    $requirements['check']['severity'] = \Drupal\Core\Extension\Requirement\RequirementSeverity::Warning;
    $requirements['check']['severity'] = \Drupal\Core\Extension\Requirement\RequirementSeverity::Error;
}
?>
