<?php

$css = $this->config('system.performance')->get('css.gzip');
$js = $this->config('system.performance')->get('js.gzip');
$this->config('system.performance')->set('css.gzip', TRUE);

?>
-----
<?php

$css = $this->config('system.performance')->get('css.gzip');
$js = $this->config('system.performance')->get('js.gzip');
$this->config('system.performance')->set('css.gzip', TRUE);

?>
