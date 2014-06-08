<?php
header ("content-type: text/xml");
// A length of 50 corresponds roughly to how much can be seen without scrolling.
readfile("http://127.0.0.1:8080/suggest?str=".urlencode($searchString) ."&sort=1&size=50");
?>
