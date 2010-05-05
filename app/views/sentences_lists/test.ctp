<?php
header('Content-disposition: attachment; filename=toto.csv');
header('Content-type: application/text');

// Just experimenting how to force display of download box.
echo $content;
?>