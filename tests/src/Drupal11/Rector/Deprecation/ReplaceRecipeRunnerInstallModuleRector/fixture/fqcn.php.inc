<?php

\Drupal\Core\Recipe\RecipeRunner::installModule($module, $recipeConfigStorage, $context);

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\Core\Recipe\RecipeRunner::installModules([$module], $recipeConfigStorage, $context), fn() => \Drupal\Core\Recipe\RecipeRunner::installModule($module, $recipeConfigStorage, $context));

?>
