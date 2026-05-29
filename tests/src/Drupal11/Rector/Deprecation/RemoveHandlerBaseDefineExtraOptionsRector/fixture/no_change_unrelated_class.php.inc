<?php

class MyUnrelatedClass
{
    public function defineExtraOptions(&$option)
    {
        $option['key'] = ['default' => 'value'];
    }
}

?>
-----
<?php

class MyUnrelatedClass
{
    public function defineExtraOptions(&$option)
    {
        $option['key'] = ['default' => 'value'];
    }
}

?>
