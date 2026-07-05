<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

if (class_exists(\Symfony\Component\Validator\Constraint::class)) {
    return;
}

abstract class Constraint
{
}
