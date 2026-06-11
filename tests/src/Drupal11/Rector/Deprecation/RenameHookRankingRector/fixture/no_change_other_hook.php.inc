<?php

namespace Drupal\mymodule\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class SearchRankingHooks {

  // A Core Hook attribute, but a different hook name — left untouched.
  #[Hook('node_presave')]
  public function presave(): void {}

  // Already migrated — idempotent, left untouched.
  #[Hook('node_search_ranking')]
  public function nodeSearchRanking(): array {
    return [];
  }

}
