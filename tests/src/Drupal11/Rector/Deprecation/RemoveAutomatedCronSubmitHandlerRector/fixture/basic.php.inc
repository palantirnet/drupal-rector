<?php

$form['#submit'][] = 'automated_cron_settings_submit';
$form['#submit'][] = 'other_submit_handler';

?>
-----
<?php

$form['#submit'][] = 'other_submit_handler';

?>
