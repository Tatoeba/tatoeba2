<?php
header ("content-type: text/xml");
readfile("http://127.0.0.1:8080/suggest?str=$searchString&sort=1");
?>
