<?php
$time = "London: ".date("U", strtotime(" + 0 hours"));
$LA = "LA: ".date("G:i", strtotime(" -7 hours"));

echo $GMT.'<br />';
echo $LA.'<br />';

?>
