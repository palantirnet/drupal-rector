<?php

// Method call with same name — not a free function, should not be touched
class SomeClass
{
    public function process($id): void
    {
        $result = $this->editor_load($id);
    }

    private function editor_load($id)
    {
        return $id;
    }
}

?>
-----
<?php

// Method call with same name — not a free function, should not be touched
class SomeClass
{
    public function process($id): void
    {
        $result = $this->editor_load($id);
    }

    private function editor_load($id)
    {
        return $id;
    }
}

?>
