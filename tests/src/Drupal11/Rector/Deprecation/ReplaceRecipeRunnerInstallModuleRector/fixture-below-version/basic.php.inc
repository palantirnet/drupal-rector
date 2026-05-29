<?php

use Drupal\Core\Recipe\RecipeRunner;

RecipeRunner::installModule($module, $recipeConfigStorage, $context);
RecipeRunner::installModules([$module], $recipeConfigStorage, $context);

?>
-----
<?php

use Drupal\Core\Recipe\RecipeRunner;

RecipeRunner::installModule($module, $recipeConfigStorage, $context);
RecipeRunner::installModules([$module], $recipeConfigStorage, $context);

?>
