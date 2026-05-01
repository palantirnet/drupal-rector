<?php

use Drupal\Core\Database\StatementPrefetchIterator;

// Typed property of type StatementPrefetchIterator IS transformed.
class MyClass {
    public StatementPrefetchIterator $statement;
    public StatementPrefetchIterator $stmt;

    public function example(): void {
        $result = $this->statement->fetchColumn(0);
        $result2 = $this->stmt->fetchColumn(2);
    }
}

?>
-----
<?php

use Drupal\Core\Database\StatementPrefetchIterator;

// Typed property of type StatementPrefetchIterator IS transformed.
class MyClass {
    public StatementPrefetchIterator $statement;
    public StatementPrefetchIterator $stmt;

    public function example(): void {
        $result = $this->statement->fetchField(0);
        $result2 = $this->stmt->fetchField(2);
    }
}

?>
