<?php

// Verifies that a class defined inside the trait's own namespace
// (`Drupal\Component\Utility`), using the short-name `use ToStringTrait;`
// with no explicit import line, is still rewritten. PhpParser's
// NameResolver visitor should resolve the unqualified trait reference to
// the same-namespace FQCN.
namespace Drupal\Component\Utility;

class FooStringy {
    use ToStringTrait;

    public function render() {
        return 'foo';
    }
}
?>
-----
<?php

// Verifies that a class defined inside the trait's own namespace
// (`Drupal\Component\Utility`), using the short-name `use ToStringTrait;`
// with no explicit import line, is still rewritten. PhpParser's
// NameResolver visitor should resolve the unqualified trait reference to
// the same-namespace FQCN.
namespace Drupal\Component\Utility;

class FooStringy {
    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function render() {
        return 'foo';
    }
}
?>
