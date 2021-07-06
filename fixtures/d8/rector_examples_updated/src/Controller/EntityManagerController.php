<?php

namespace Drupal\rector_examples\Plugin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Examples of `$this->entityManager()` calls from a class that extends `ControllerBase`.
 */
class EntityManagerController extends ControllerBase {

  /**
   * Simple example
   *
   * @return null
   */
  public function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // We are assuming that we want to use the `$this->entityTypeManager` injected service since no method was called here directly. Please confirm this is the case. If another service is needed, you may need to inject that yourself. See https://www.drupal.org/node/2549139 for more information.
    $entity_manager = $this->entityTypeManager();

    return NULL;
  }

  /**
   * Example of using a method directly on the service.
   *
   * @return null
   */
  public function method_on_service() {
    $definitions = \Drupal::service('entity_type.manager')->getDefinitions();

    return NULL;
  }

  /**
   * Example of using a method that is not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   *
   * @return null
   */
  public function method_not_in_entityTypeManager() {
    $group = FALSE;

    $entity_manager = \Drupal::service('entity_type.repository')->getEntityTypeLabels($group);

    return NULL;
  }

  /**
   * Example of storing the services and then calling methods not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   *
   * @return null
   */
  public function stored_service_and_method_not_in_entityTypeManager() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // We are assuming that we want to use the `$this->entityTypeManager` injected service since no method was called here directly. Please confirm this is the case. If another service is needed, you may need to inject that yourself. See https://www.drupal.org/node/2549139 for more information.
    $entity_manager = $this->entityTypeManager();

    $group = FALSE;
    $class_name = 'MyClass';

    $entity_manager->getEntityTypeLabels($group);
    $entity_manager->getEntityTypeFromClass($class_name);

    return NULL;
  }

}
