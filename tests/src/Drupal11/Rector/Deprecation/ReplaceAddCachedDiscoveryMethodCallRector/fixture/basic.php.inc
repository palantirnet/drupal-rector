<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

function my_module_service_provider(ContainerBuilder $container): void
{
    $container->getDefinition('plugin.cache_clearer')
        ->addMethodCall('addCachedDiscovery', [new Reference('my.plugin.manager')]);
}
?>
-----
<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

function my_module_service_provider(ContainerBuilder $container): void
{
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => $container->getDefinition('my.plugin.manager')->addTag('plugin_manager_cache_clear'), fn() => $container->getDefinition('plugin.cache_clearer')
        ->addMethodCall('addCachedDiscovery', [new Reference('my.plugin.manager')]));
}
?>
