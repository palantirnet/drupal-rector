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
        $result = $statement->fetchField(0);
        $other = $this->clientStatement->fetchColumn(0);
    }
}
?>
