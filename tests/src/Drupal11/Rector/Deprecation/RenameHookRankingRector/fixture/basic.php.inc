<?php

namespace Drupal\mymodule\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class SearchRankingHooks {

  #[Hook('ranking')]
  public function ranking(): array {
    return [];
  }

}

?>
-----
<?php

namespace Drupal\mymodule\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class SearchRankingHooks {

  #[Hook('node_search_ranking')]
  public function ranking(): array {
    return [];
  }

}

?>
