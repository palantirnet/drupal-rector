<?php

class MyHandlerFqcn extends \Drupal\views\Plugin\views\HandlerBase
{
    public function defineExtraOptions(&$option)
    {
        $option['fqcn_key'] = ['default' => 0];
    }
}

?>
-----
<?php

class MyHandlerFqcn extends \Drupal\views\Plugin\views\HandlerBase
{
}

?>
