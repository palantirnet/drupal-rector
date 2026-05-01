<?php

use Drupal\views\Plugin\views\filter\FilterPluginBase;

class MyFilter extends FilterPluginBase
{
    public function defineExtraOptions(&$option)
    {
        $option['filter_key'] = ['default' => FALSE];
    }

    public function adminSummary()
    {
        return 'summary';
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\filter\FilterPluginBase;

class MyFilter extends FilterPluginBase
{
    public function adminSummary()
    {
        return 'summary';
    }
}

?>
