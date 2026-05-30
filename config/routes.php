<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
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
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */

use App\Controller\Component\RememberMeComponent;
use App\Middleware\LanguageSelectorMiddleware;
use AssetCompress\Middleware\AssetCompressMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use App\Middleware\LegacyEncryptedCookieMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Utility\Security;


/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(InflectedRoute::class);

$routes->scope('/', function (RouteBuilder $routes) {
    $routes->registerMiddleware('languageSelector', new LanguageSelectorMiddleware());

    $routes->registerMiddleware('asset', new AssetMiddleware([
        'cacheTime' => Configure::read('Asset.cacheTime')
    ]));

    $routes->registerMiddleware('assetCompress', new AssetCompressMiddleware());

    $routes->registerMiddleware('encryptedCookie', new LegacyEncryptedCookieMiddleware(
        // Names of cookies to protect
        [RememberMeComponent::getCookieName()],
        Configure::read('Security.cookieKey'),
        Security::getSalt()
    ));

    $routes->registerMiddleware('csrfProtection', new CsrfProtectionMiddleware());
});

$routes->scope('/', ['prefix' => 'VHosts/Api'], function (RouteBuilder $routes) {
    $routes->connect(
        '/',
        ['controller' => 'doc', 'action' => 'index']
    )
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/examples/{name}',
        ['controller' => 'doc', 'action' => 'examples']
    )
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/openapi',
        ['controller' => 'doc', 'action' => 'show']
    )
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/{version}',
        ['controller' => 'api']
    )
    ->setPersist(['version'])
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/{version}/{controller}',
        ['action' => 'search']
    )
    ->setPersist(['version'])
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/{version}/{controller}/{id}',
        ['action' => 'get']
    )
    ->setPass(['id'])
    ->setPersist(['version'])
    ->setMethods(['GET'])
    ->setHost('api.*');

    $routes->connect(
        '/{version}/{controller}/{id}/{action}'
    )
    ->setPass(['id'])
    ->setPersist(['version'])
    ->setMethods(['GET'])
    ->setHost('api.*');
});

$routes->scope('/', ['prefix' => 'VHosts/Audio'], function (RouteBuilder $routes) {
    $routes->connect(
        '/sentences/{lang}/{sentence_id}.mp3',
        ['controller' => 'main', 'action' => 'legacy_audio_url']
    )
    ->setHost('audio.*')
    ->setPass(['lang', 'sentence_id'])
    ->setPatterns(['sentence_id' => '\d+']);

    $routes->connect(
        '/*',
        ['controller' => 'main', 'action' => 'default']
    )
    ->setHost('audio.*');
});

$routes->scope('/', function (RouteBuilder $routes) {
    $routes->applyMiddleware('assetCompress');

    // Handle plugin/theme assets like CakePHP normally does.
    $routes->applyMiddleware('asset');

    $routes->applyMiddleware('languageSelector');

    $routes->applyMiddleware('encryptedCookie');

    // Add csrf middleware.
    $routes->applyMiddleware('csrfProtection');

    // Regex pattern for language parameter
    $langPattern = join(
        '|',
        array_keys(Configure::read('UI.languages'))
    );

    $routes->connect(
        '/{lang}',
        ['controller' => 'Pages', 'action' => 'index']
    )
    ->setPatterns(['lang' => $langPattern])
    ->setPersist(['lang']);

    $routes->connect(
        '/',
        ['controller' => 'Pages', 'action' => 'index']
    );

    $routes->connect(
        '/{lang}/{action}',
        ['controller' => 'Pages']
    )
    ->setPatterns(['lang' => $langPattern])
    ->setPersist(['lang']);

    $routes->connect(
        '/{action}',
        ['controller' => 'Pages']
    );

    $routes->connect('/{lang}/{controller}/{action}/*')
    ->setPatterns(['lang' => $langPattern])
    ->setPersist(['lang']);

    $routes->connect('/{controller}/{action}/*');
});
