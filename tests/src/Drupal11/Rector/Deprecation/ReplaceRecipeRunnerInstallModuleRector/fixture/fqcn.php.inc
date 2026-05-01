<?php

\Drupal\Core\Recipe\RecipeRunner::installModule($module, $recipeConfigStorage, $context);

?>
-----
<?php

\Drupal\Core\Recipe\RecipeRunner::installModules([$module], $recipeConfigStorage, $context);

?>
