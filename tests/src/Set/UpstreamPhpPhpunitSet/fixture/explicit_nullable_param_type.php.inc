<?php

namespace Drupal\Tests\my_module;

use Drupal\Core\Entity\EntityInterface;

class NullableParams {

  public function build(EntityInterface $entity = NULL, array $options = NULL, string|int $id = NULL, $untyped = NULL, ?string $already = NULL, mixed $any = NULL): void {
  }

}

function my_module_nullable_helper(\Countable&\Traversable $both, callable $callback = null) {
}

?>
-----
<?php

namespace Drupal\Tests\my_module;

use Drupal\Core\Entity\EntityInterface;

class NullableParams {

  public function build(?EntityInterface $entity = NULL, ?array $options = NULL, string|int|null $id = NULL, $untyped = NULL, ?string $already = NULL, mixed $any = NULL): void {
  }

}

function my_module_nullable_helper(\Countable&\Traversable $both, ?callable $callback = null) {
}

?>
