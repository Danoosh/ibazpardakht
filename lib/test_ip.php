<?php

$content = file_get_contents('http://api.ipresta.ir/ip/get_ip.php');

preg_match('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $content, $matches);

echo $matches[0];
?>