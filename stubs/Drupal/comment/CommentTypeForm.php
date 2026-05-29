<?php

declare(strict_types=1);

namespace Drupal\comment;

if (class_exists(\Drupal\comment\CommentTypeForm::class)) {
    return;
}

class CommentTypeForm {}
