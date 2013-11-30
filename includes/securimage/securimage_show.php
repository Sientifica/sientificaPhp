<?php

include 'securimage.php';
header ("Content-type: image/png");
$img = new securimage();

$img->show(); // alternate use:  $img->show('/path/to/background.jpg');

?>
