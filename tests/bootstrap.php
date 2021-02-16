<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
use \Cake\Cache\Cache;
use \Cake\Core\Configure;

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

// Make sure search is disabled in order to prevent
// update of Sphinx attributes in SphinxBehavior.
Configure::write('Search.enabled', false);

// Avoid caching any data produced by tests
Cache::disable();
