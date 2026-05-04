<?php
declare(strict_types=1);

/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

if (empty($_SERVER['HTTP_HOST']) && !Configure::read('App.fullBaseUrl')) {
    Configure::write('App.fullBaseUrl', 'http://localhost');
}

// Fixate sessionid early on, as php7.2+
// does not allow the sessionid to be set after stdout
// has been written to.
session_id('cli');

// Make sure search is disabled in order to prevent
// update of Sphinx attributes in SphinxBehavior.
Configure::write('Search.enabled', false);

// Disable transcriptions by default
// This can be activated for some specific tests
Configure::write('AutoTranscriptions.enabled', false);

// Avoid caching any data produced by tests
Cache::disable();
