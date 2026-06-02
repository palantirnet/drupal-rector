<?php

declare(strict_types=1);

namespace Drupal\Tests\comment\Functional;

use Drupal\Tests\BrowserTestBase;

if (class_exists(\Drupal\Tests\comment\Functional\CommentTestBase::class)) {
    return;
}

abstract class CommentTestBase extends BrowserTestBase
{
    public function setCommentPreview($mode, $field_name = 'comment'): void
    {
    }
}
