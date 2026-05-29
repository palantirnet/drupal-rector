<?php

use Drupal\Core\Database\StatementPrefetchIterator;

class MyClass {
    public function example(StatementPrefetchIterator $statement): void {
        $result = $statement->fetchColumn(0);
        $other = $this->clientStatement->fetchColumn(0);
    }
}
?>
-----
<?php

use Drupal\Core\Database\StatementPrefetchIterator;

class MyClass {
    public function example(StatementPrefetchIterator $statement): void {
        $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $statement->fetchField(0), fn() => $statement->fetchColumn(0));
        $other = $this->clientStatement->fetchColumn(0);
    }
}
?>
