<?php

function example(array $theme_groups): void {
    uasort($theme_groups['installed'], 'system_sort_themes');
}
?>
-----
<?php

function example(array $theme_groups): void {
    uasort($theme_groups['installed'], static function ($a, $b) {
        if ($a->is_default) {
            return -1;
        }
        if ($b->is_default) {
            return 1;
        }
        return strcasecmp($a->info['name'], $b->info['name']);
    });
}
?>
