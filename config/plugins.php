<?php
/**
 * Plugin configuration.
 *
 * In this file, you configure which plugins are loaded in the different states your app can be.
 * It's loaded via the `parent::bootstrap();` call inside your `Application::bootstrap()` method.
 * For more information see https://book.cakephp.org/5/en/plugins.html#loading-plugins-via-configuration-array
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         5.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

 /*
  * List of plugins to load in the form `PluginName` => `[configuration options]`.
  *
  * Available options:
  * - onlyDebug: Load the plugin only in debug mode. Default false.
  * - onlyCli: Load the plugin only in CLI mode. Default false.
  * - optional: Do not throw an exception if the plugin is not found. Default false.
  */
return [
    'DebugKit' => ['onlyDebug' => true, 'optional' => true],
    'Bake' => ['onlyCli' => true, 'optional' => true],
    'Migrations' => ['onlyCli' => true],

    // Additional plugins here
    'Queue' => ['bootstrap' => true, 'routes' => false],
    'AssetCompress' => ['middleware' => false],
    'TinyAuth',
    'Authentication',
    'Authorization',
];
