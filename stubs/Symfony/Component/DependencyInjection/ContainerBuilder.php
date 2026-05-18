<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection;

if (class_exists(\Symfony\Component\DependencyInjection\ContainerBuilder::class)) {
    return;
}

class ContainerBuilder
{
    public function getDefinition(string $id): Definition {}
}
