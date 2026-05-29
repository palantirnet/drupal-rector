<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection;

if (class_exists(\Symfony\Component\DependencyInjection\Reference::class)) {
    return;
}

class Reference
{
    public function __construct(string $id, int $invalidBehavior = 1) {}
}
