<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Cache\Cache;
use Cake\Chronos\Chronos;
use Cake\Core\Configure;
use Cake\TestSuite\ConnectionHelper;
use Cake\TestSuite\Fixture\SchemaLoader;
use Migrations\Migrations;

/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

if (empty($_SERVER['HTTP_HOST']) && !Configure::read('App.fullBaseUrl')) {
    Configure::write('App.fullBaseUrl', 'http://localhost');
}

// Fixate now to avoid one-second-leap-issues
Chronos::setTestNow(Chronos::now());

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

Configure::write('Tatoeba.communityModeratorEmail', 'moderator@example.net');

// Avoid caching any data produced by tests
Cache::disable();

// Connection aliasing needs to happen before migrations are run.
// Otherwise, table objects inside migrations would use the default datasource
ConnectionHelper::addTestAliases();

// Use migrations to build test database schema.
//
// Will rebuild the database if the migration state differs
// from the migration history in files.
//
// Create database schema for fixtures
$sqlFiles = glob(dirname(__DIR__) . '/docs/database/tables/*.sql');

$loader = new SchemaLoader();
$loader->loadSqlFiles($sqlFiles, 'test', true, true);
$loader->loadSqlFiles(dirname(__DIR__) . '/docs/database/tatowiki/articles.sql', 'test_wiki');

$migrations = new Migrations();
$migrations->migrate(['connection' => 'test']);
$migrations->migrate(['connection' => 'test', 'plugin' => 'Queue']);
