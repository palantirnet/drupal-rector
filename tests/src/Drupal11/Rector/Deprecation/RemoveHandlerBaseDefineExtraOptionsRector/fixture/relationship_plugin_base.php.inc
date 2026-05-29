<?php

use Drupal\views\Plugin\views\relationship\RelationshipPluginBase;

class MyRelationship extends RelationshipPluginBase
{
    public function defineExtraOptions(&$option)
    {
        $option['relationship_key'] = ['default' => ''];
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\relationship\RelationshipPluginBase;

class MyRelationship extends RelationshipPluginBase
{
}

?>
