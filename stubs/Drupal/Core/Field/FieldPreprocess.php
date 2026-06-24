<?php

declare(strict_types=1);

namespace Drupal\Core\Field;

if (class_exists(\Drupal\Core\Field\FieldPreprocess::class)) {
    return;
}

class FieldPreprocess
{
    public function preprocessField(&$variables): void {}

    public function preprocessFieldMultipleValueForm(&$variables): void {}
}
