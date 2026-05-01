<?php

declare(strict_types=1);

namespace Drupal\Core\Entity;

if (interface_exists(\Drupal\Core\Entity\EntityTypeInterface::class)) {
    return;
}

interface EntityTypeInterface
{
    public function setLinkTemplate(string $key, string $path): static;
}
