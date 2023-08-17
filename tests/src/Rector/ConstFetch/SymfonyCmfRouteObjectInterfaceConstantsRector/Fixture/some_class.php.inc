<?php

namespace Rector\Tests\DrupalRector\Rector\ConstFetch\SymfonyCmfRouteObjectInterfaceConstantsRector\Fixture;

class SomeClass
{
    public const NAME = \Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_NAME;
    public const OBJECT = \Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT;
    public const CONTROLLER = \Symfony\Cmf\Component\Routing\RouteObjectInterface::CONTROLLER_NAME;
}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\ConstFetch\SymfonyCmfRouteObjectInterfaceConstantsRector\Fixture;

class SomeClass
{
    public const NAME = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
    public const OBJECT = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
    public const CONTROLLER = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
}

?>
