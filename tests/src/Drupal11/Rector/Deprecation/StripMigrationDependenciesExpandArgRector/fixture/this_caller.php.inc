<?php

// $this in a Migration subclass is not transformed — PHPStan cannot resolve
// the type of $this without Drupal core in the analysis path, so the
// MigrationInterface type guard does not fire.
class MyMigration extends \Drupal\migrate\Plugin\Migration {
    public function run(): array {
        return $this->getMigrationDependencies(TRUE);
    }
}

?>
-----
<?php

// $this in a Migration subclass is not transformed — PHPStan cannot resolve
// the type of $this without Drupal core in the analysis path, so the
// MigrationInterface type guard does not fire.
class MyMigration extends \Drupal\migrate\Plugin\Migration {
    public function run(): array {
        return $this->getMigrationDependencies(TRUE);
    }
}

?>
