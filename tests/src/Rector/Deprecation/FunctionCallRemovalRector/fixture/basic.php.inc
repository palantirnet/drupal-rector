<?php

function example($block_type_id, $label, $variables, $form, $form_state) {
    block_content_add_body_field($block_type_id, $label);
    template_preprocess($variables, 'mytemplate');
    update_clear_update_disk_cache();
    update_delete_file_if_stale('/tmp/file.tar.gz');
    _update_manager_cache_directory(FALSE);
    _update_manager_extract_directory(FALSE);
    _update_manager_unique_identifier();
    views_ui_contextual_links_suppress();
    views_ui_contextual_links_suppress_push();
    views_ui_contextual_links_suppress_pop();
    automated_cron_settings_submit($form, $form_state);
    other_function($form, $form_state);
}
?>
-----
<?php

function example($block_type_id, $label, $variables, $form, $form_state) {
    other_function($form, $form_state);
}
?>
