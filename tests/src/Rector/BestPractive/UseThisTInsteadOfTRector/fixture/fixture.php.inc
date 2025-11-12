<?php
class TestT {
    public function t($string) {
        return $string;
    }
}

class NoT extends TestT {

    public function doThings() {
        return t('Hello world');
    }

}

?>
-----
<?php
class TestT {
    public function t($string) {
        return $string;
    }
}

class NoT extends TestT {

    public function doThings() {
        return $this->t('Hello world');
    }

}

?>
