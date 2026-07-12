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
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * This file is loaded by your src/Application.php bootstrap method.
 * Feel free to extend/extract parts of the bootstrap process into your own files
 * to suit your needs/preferences.
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'paths.php';

/*
 * Bootstrap CakePHP
 * Currently all this does is initialize the router (without loading your routes)
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Detection\MobileDetect;
use function Cake\Core\env;

/*
 * Load global functions for collections, translations, debugging etc.
 */
require CAKE . 'functions.php';

/*
 * See https://github.com/josegonzalez/php-dotenv for API details.
 *
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.example` to `config/.env` and set/modify the
 * variables as required.
 *
 * The purpose of the .env file is to emulate the presence of the environment
 * variables like they would be present in production.
 *
 * If you use .env files, be careful to not commit them to source control to avoid
 * security risks. See https://github.com/josegonzalez/php-dotenv#general-security-information
 * for more information for recommended practices.
*/
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Initializes default Config store and loads the main configuration file (app.php)
 *
 * CakePHP contains 2 configuration files after project creation:
 * - `config/app.php` for the default application configuration.
 * - `config/app_local.php` for environment specific configuration.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file to provide overrides to your configuration.
 * Notice: For security reasons app_local.php **should not** be included in your git repo.
 */
if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

/*
 * When debug = true the metadata cache should only last for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+1 minute');
    Configure::write('Cache._cake_translations_.duration', '+1 minute');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');

    /*
     * Make cache files world-writable on dev environments
     * to ease file permissions setup.
     */
    $confCache = Configure::read('Cache');
    foreach ($confCache as $cache => $config) {
        if (Configure::read('debug')) {
            $confCache[$cache]['mask'] = 0666;
        }
    }
    Configure::write('Cache', $confCache);
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check https://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$errorTrap = new ErrorTrap(Configure::read('Error'));
$errorTrap->getEventManager()->on(
    'Error.beforeRender',
    function (\Cake\Event\EventInterface $event, \Cake\Error\PhpError $error) {
        $request = \Cake\Routing\Router::getRequest();

        // Prevent messing up with json output;
        // only log error to debug.log
        if ($request && $request->accepts('application/json')) {
            $event->stopPropagation();
        }
    }
);
$errorTrap->register();
(new ExceptionTrap(Configure::read('Error')))->register();

/*
 * CLI/Command specific configuration.
 */
if (PHP_SAPI === 'cli') {
    // Set the fullBaseUrl to allow URLs to be generated in commands.
    // This is useful when sending email from commands.
    // Configure::write('App.fullBaseUrl', php_uname('n'));

    // Set logs to different files so they don't have permission conflicts.
    if (Configure::check('Log.debug')) {
        Configure::write('Log.debug.file', 'cli-debug');
    }
    if (Configure::check('Log.error')) {
        Configure::write('Log.error.file', 'cli-error');
    }
}

/*
 * Set the full base URL for the application.
 *
 * SECURITY: In production, App.fullBaseUrl MUST be explicitly configured to prevent
 * Host Header Injection attacks. The HostHeaderMiddleware enforces this requirement
 * and validates incoming Host headers against the configured value.
 *
 * Set APP_FULL_BASE_URL in your environment variables or configure App.fullBaseUrl
 * in config/app.php or config/app_local.php
 *
 * Example: APP_FULL_BASE_URL=https://example.com
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');
if (!$fullBaseUrl) {
    $httpHost = env('HTTP_HOST');

    /*
     * Development mode fallback: Use HTTP_HOST for convenience.
     * WARNING: This is ONLY safe in development. In production, the
     * HostHeaderMiddleware will reject requests when fullBaseUrl is not configured.
     */
    if ($httpHost) {
        $s = null;
        if (env('HTTPS') || env('HTTP_X_FORWARDED_PROTO') === 'https') {
            $s = 's';
        }
        $fullBaseUrl = 'http' . $s . '://' . $httpHost;
    }
    unset($httpHost, $s);
}
if ($fullBaseUrl) {
    Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

/*
 * Apply the loaded configuration settings to their respective systems.
 * This will also remove the loaded config data from memory.
 */
Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Setup detectors for mobile and tablet.
 * If you don't use these checks you can safely remove this code
 * and the mobiledetect package from composer.json.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new MobileDetect();

    return $detector->isTablet();
});

/*
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats when processing request data. For details see
 * @link https://book.cakephp.org/5/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
// \Cake\Database\TypeFactory::build('time')->useLocaleParser();
// \Cake\Database\TypeFactory::build('date')->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetime')->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestamp')->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetimefractional')->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestampfractional')->useLocaleParser();
// \Cake\Database\TypeFactory::build('datetimetimezone')->useLocaleParser();
// \Cake\Database\TypeFactory::build('timestamptimezone')->useLocaleParser();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
// \Cake\Utility\Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
// \Cake\Utility\Inflector::rules('irregular', ['red' => 'redlings']);
// \Cake\Utility\Inflector::rules('uninflected', ['dontinflectme']);

/**
 * Printf-like function that supports:
 *   {n} params, n integer starting at zero
 *   {n.key} params passing array('key' => 'string')
 *   {n.key} params passing '; key: string; ...' strings
 *   {key.subkey} params passing array('key' => '; subkey: string; ...')
 *   if n is not specified, it is assumed starting at zero
 *
 * If a key is not found within the '; ...' list, it takes the first one
 *
 * See tests/TestCase/BootstrapTest.php for more infos.
 */
if (!function_exists('format')) {
    function format() {
        $args = func_get_args();
        $format = array_shift($args);
        if (count($args) && is_array($args[0]))
            $args = $args[0];

        return preg_replace_callback('/\{([^}.]+)?(\.([^}]+))?\}/', function($matches) use($args) {
            static $i = 0;
            $key    = isset($matches[1]) && $matches[1] != '' ? $matches[1] : $i++;
            $subkey = isset($matches[3]) && $matches[3] != '' ? $matches[3] : null;
            $res = '';
            if (array_key_exists($key, $args)) {
                $res = $args[$key];
                $list = __format_decompose_list((string)$res);
                if (is_array($list)) {
                    reset($list);
                    if (!$subkey || !array_key_exists($subkey, $list))
                        $subkey = key($list);
                    $res = array_key_exists($subkey, $list) ? $list[$subkey] : '';
                }
            }
            return $res;
        }, $format);
    }
}
if (!function_exists('__format_decompose_list')) {
    function __format_decompose_list($string) {
        $result = $string;
        if ($string !== '' && $string[0] == ';') {
            $list = explode(';', $string);
            $result = array();
            array_shift($list);
            foreach ($list as $kv_str) {
                $keyvalue = explode(':', $kv_str, 2);
                if (count($keyvalue) == 2) {
                    $result[trim($keyvalue[0])] = trim($keyvalue[1]);
                }
            }
        }
        return $result;
    }
}

// set a custom date and time format
// see https://book.cakephp.org/5/en/core-libraries/time.html#setting-the-default-locale-and-format-string
// and https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax
\Cake\I18n\Date::setToStringFormat('yyyy-MM-dd');
\Cake\I18n\Time::setToStringFormat('yyyy-MM-dd HH:mm:ss'); // TODO needed?
\Cake\I18n\DateTime::setToStringFormat('yyyy-MM-dd HH:mm:ss');
\Cake\I18n\DateTime::$niceFormat = [\IntlDateFormatter::LONG, \IntlDateFormatter::LONG];

Cake\I18n\I18n::setDefaultFormatter('sprintf');
Cake\I18n\I18n::useFallback(false);
Cake\Validation\Validator::addDefaultProvider('appvalidation', 'App\Validation\Validation');

/* Work around missing pcntl definitions in php-fpm context (not php-cli) */
if (!defined('SIGUSR1')) {
    define('SIGUSR1', 10);
}

if (!defined('SIGTERM')) {
    define('SIGTERM', 15);
}
