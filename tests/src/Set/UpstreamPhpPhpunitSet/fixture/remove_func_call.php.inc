<?php

function my_module_close_handles($ch, $share, $finfo, $image, $parser) {
  curl_close($ch);
  curl_share_close($share);
  finfo_close($finfo);
  imagedestroy($image);
  xml_parser_free($parser);
  \curl_close($ch);
  curl_exec($ch);
}

?>
-----
<?php

function my_module_close_handles($ch, $share, $finfo, $image, $parser) {
  curl_exec($ch);
}

?>
