<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection;

if (class_exists(\Symfony\Component\DependencyInjection\Definition::class)) {
    return;
}

class Definition
{
    public function addMethodCall(string $method, array $arguments = [], bool $returnsClone = false): static {}

    public function addTag(string $name, array $attributes = []): static {}
}
