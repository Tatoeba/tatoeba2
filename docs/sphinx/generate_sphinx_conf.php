<?php
define('__TAT_ROOT__', dirname(dirname(dirname(__FILE__))));
passthru(sprintf('"%s"/cake/console/cake -app "%s"/app sphinx_conf', __TAT_ROOT__, __TAT_ROOT__));
