<?php

namespace Drupal\Tests\rector_fixture {

class SomeOtherCommand
{
    public function __construct(string $a, string $b, mixed $c, array $options) {}
}

function unrelated(string $content): SomeOtherCommand
{
    return new SomeOtherCommand('#my-dialog', 'Title', $content, ['dialogClass' => 'should-not-change']);
}

}
?>
