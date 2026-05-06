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
        $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $this->statement->fetchField(0), fn() => $this->statement->fetchColumn(0));
        $result2 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $this->stmt->fetchField(2), fn() => $this->stmt->fetchColumn(2));
    }
}

?>
