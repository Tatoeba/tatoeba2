<?php
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
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\MapResolver;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\ServerRequest;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use CakeDC\CachedRouting\Routing\Middleware\CachedRoutingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TinyAuth\Policy\RequestPolicy;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
    const QUERY_PARAM_REDIRECT = 'redirect';

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        // Define when and where users should be redirected to when they are not authenticated
        if (!$request->is('ajax')) {
            $service->setConfig([
                'unauthenticatedRedirect' => Router::url([
                    'prefix' => false,
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'login',
                ]),
                'queryParam' => self::QUERY_PARAM_REDIRECT,
            ]);
        }

        $fields = [
            IdentifierInterface::CREDENTIAL_USERNAME => 'username',
            IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
        ];

        // Load the authenticators. Session should be first.
        $service->loadAuthenticator('Authentication.Session', [
            'sessionKey' => 'Auth.User',
            'identify' => true,
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => Router::url([
                'prefix' => false,
                'plugin' => false,
                'controller' => 'Users',
                'action' => 'check_login',
            ]),
        ]);

        // Load identifiers
        $service->loadIdentifier('Authentication.Password', [
            'fields' => $fields,
            'resolver' => [
                'className' => 'Authentication.Orm',
                'finder' => 'userToLogin',
            ],
            'passwordHasher'=> [
                'className' => 'Versioned'
            ],
        ]);

        return $service;
    }

    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        // Map ServerRequest to TinyAuth's RequestPolicy for INI-based RBAC
        $mapResolver = new MapResolver();
        $policy = new RequestPolicy([
            'includeAuthentication' => true,  // authorize any public action in auth_allow.ini
            'roleColumn' => 'role',           // use users.role database field to get user role
        ]);
        $mapResolver->map(ServerRequest::class, $policy);

        return new AuthorizationService($mapResolver);
    }

    public function bootstrap(): void
    {
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            try {
                $this->addPlugin('Bake');
            } catch (MissingPluginException $e) {
                // Do not halt if the plugin is missing
            }
            $this->addPlugin('Migrations');

            // reload app commands once at the end
            // this allow app commands to override plugin commands
            $this->getEventManager()->on(
                'Console.buildCommands',
                function (\Cake\Event\Event $event) {
                    $commands = $event->getData('commands');
                    $commands->addMany($commands->autoDiscover());
                }
            );
        }

        $this->addPlugin('Queue', ['bootstrap' => true, 'routes' => false]);
        $this->addPlugin('AssetCompress', ['middleware' => false]);
        $this->addPlugin('TinyAuth');
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middlewareQueue): \Cake\Http\MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Add routing middleware.
            ->add(new CachedRoutingMiddleware($this, '_cake_routes_'));

            // Other middlewares are added from config/routes.php

        return $middlewareQueue;
    }
}
