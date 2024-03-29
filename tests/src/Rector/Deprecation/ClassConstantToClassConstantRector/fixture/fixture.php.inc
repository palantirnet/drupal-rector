<?php

function simple_example() {
    $value = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_NAME;
    $value2 = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT;
    $value3 = Symfony\Cmf\Component\Routing\RouteObjectInterface::CONTROLLER_NAME;
}

?>
-----
<?php

function simple_example() {
    $value = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
    $value2 = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
    $value3 = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
}

?>
