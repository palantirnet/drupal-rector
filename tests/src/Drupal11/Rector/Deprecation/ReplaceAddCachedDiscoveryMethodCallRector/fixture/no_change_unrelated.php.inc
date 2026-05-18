<?php

// Object without type annotation - not a Symfony Definition; should not be transformed.
$someObject->addMethodCall('addCachedDiscovery', [new \Symfony\Component\DependencyInjection\Reference('my.plugin.manager')]);
?>
-----
<?php

// Object without type annotation - not a Symfony Definition; should not be transformed.
$someObject->addMethodCall('addCachedDiscovery', [new \Symfony\Component\DependencyInjection\Reference('my.plugin.manager')]);
?>
