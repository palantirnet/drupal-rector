<?php

class MyClass {
    public function example($statement) {
        $result = $statement->fetchColumn(0);
        $other = $this->clientStatement->fetchColumn(0);
    }
}
?>
-----
<?php

class MyClass {
    public function example($statement) {
        $result = $statement->fetchField(0);
        $other = $this->clientStatement->fetchColumn(0);
    }
}
?>
