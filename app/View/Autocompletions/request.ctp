<?php
readfile("http://127.0.0.1:8080/suggest?str=".urlencode($searchString) ."&sort=1&size=10");
?>
