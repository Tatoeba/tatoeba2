<?php
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
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use App\Error\TatoebaErrorHandler;
use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Inflector;
use Cake\Utility\Security;

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 */
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load tatoeba specific configuration file.
 */
Configure::load('app_local', 'default');

if (Configure::read('debug')) {
    /*
     * When debug = true the metadata cache should only last
     * for a short time.
     */
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');

    /**
     * In debug mode errors and warnings will be displayed in the browser.
     * But whenever the output contains {{ the app will crash because they
     * will be interpreted by the AngularJS compiler.
     * We can prevent the crash if we modify the template string by adding
     * the ng-non-bindable directive. Unfortunately the template string
     * isn't easily accessible so we need to use PHP's Reflection feature.
     */
    $debugger = new ReflectionClass('Cake\Error\Debugger');
    $templates = $debugger->getProperty('_templates');
    $templates->setAccessible(true);
    $error = $templates->getValue(Debugger::getInstance())['js']['error'];
    $error = preg_replace('/>/', ' ng-non-bindable>', $error, 1);
    Debugger::addFormat('js', ['error' => $error]);

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
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
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
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new TatoebaErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Email::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
Type::build('time')
    ->useImmutable();
Type::build('date')
    ->useImmutable();
Type::build('datetime')
    ->useImmutable();
Type::build('timestamp')
    ->useImmutable();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

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

Cake\I18n\I18n::setDefaultFormatter('sprintf');
Cake\I18n\I18n::useFallback(false);
Cake\I18n\Time::setToStringFormat('yyyy-MM-dd HH:mm:ss');
Cake\I18n\Time::$niceFormat = [\IntlDateFormatter::LONG, \IntlDateFormatter::SHORT];
Cake\I18n\FrozenTime::setToStringFormat('yyyy-MM-dd HH:mm:ss');
Cake\I18n\FrozenTime::$niceFormat = [\IntlDateFormatter::LONG, \IntlDateFormatter::LONG];

Cake\Validation\Validator::addDefaultProvider('appvalidation', 'App\Validation\Validation');
