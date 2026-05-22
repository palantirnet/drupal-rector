<?php

function example($form, $form_state) {
    syslog_facility_list();
    syslog_logging_settings_submit($form, $form_state);
    other_function();
}
?>
-----
<?php

function example($form, $form_state) {
    other_function();
}
?>
