<?php
declare(strict_types=1);

namespace App\Authenticator;

use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\CookieAuthenticator;
use Authentication\Authenticator\PersistenceInterface;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Authentication\UrlChecker\UrlCheckerTrait;
use Cake\Core\Configure;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieInterface;
use Cake\Utility\CookieCryptTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Tatoeba's humble "remember me" cookie-based authentication.
 */
class RememberMeAuthenticator extends AbstractAuthenticator implements PersistenceInterface
{
    use CookieCryptTrait;
    use UrlCheckerTrait;

    protected $_defaultConfig = [
        'loginUrl' => null,
        'urlChecker' => 'Authentication.Default',
        'rememberMeField' => 'rememberMe',
        'cookie' => [
            'name' => 'User',
            'expires' => '2 weeks',
            'httponly' => true,
        ],
    ];

    protected function _getCookieEncryptionKey(): string
    {
        return Configure::read('Security.cookieKey');
    }

    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $cookies = $request->getCookieParams();
        $cookieName = $this->getConfig('cookie.name');
        if (!isset($cookies[$cookieName])) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING, [
                'Login credentials not found',
            ]);
        }

        $identifier = $this->_decrypt($cookies[$cookieName], 'aes');
        if ($identifier === '') {
            // TODO: remove after 2026/07/04
            $identifier = $this->_decrypt($cookies[$cookieName], 'aes', \Cake\Utility\Security::getSalt());
        }

        if (!is_array($identifier)
            || count($identifier) !== 2
            || !is_string($identifier['username'] ?? null)
            || !is_string($identifier['password'] ?? null)) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'Cookie is invalid.',
            ]);
        }

        ['username' => $username, 'password' => $password] = $identifier;

        $identity = $this->_identifier->identify(compact('username'));

        if (empty($identity)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        if ($password !== $identity['password']) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'Hashed password does not match',
            ]);
        }

        return new Result($identity, Result::SUCCESS);
    }

    protected function _needsNewCookie(ServerRequestInterface $request): bool
    {
        // Did we just logged in with "remember me" checked?
        if ($this->_checkUrl($request)) {
            $field = $this->getConfig('rememberMeField');
            $bodyData = $request->getParsedBody();
            if (is_array($bodyData) && !empty($bodyData[$field])) {
                return true;
            }
        }

        // Did we just authenticated with a cookie? Then refresh it.
        if ($this === $request->getAttribute('authentication')->getAuthenticationProvider()) {
            return true;
        }

        return false;
    }

    public function persistIdentity(ServerRequestInterface $request, ResponseInterface $response, $identity): array
    {
        if ($this->_needsNewCookie($request)) {
            $cookie = $this->_createCookie($identity);
            $response = $response->withAddedHeader('Set-Cookie', $cookie->toHeaderValue());
        }

        return [
            'request' => $request,
            'response' => $response,
        ];
    }

    public function clearIdentity(ServerRequestInterface $request, ResponseInterface $response): array
    {
        $cookie = $this->_createCookie(null)->withExpired();

        return [
            'request' => $request,
            'response' => $response->withAddedHeader('Set-Cookie', $cookie->toHeaderValue()),
        ];
    }

    protected function _createCookie($identity): CookieInterface
    {
        $options = $this->getConfig('cookie');
        $name = $options['name'];
        unset($options['name']);

        $value = $identity ?
            $this->_encrypt(
                [
                    'username' => $identity['username'],
                    'password' => $identity['password'],
                ],
                'aes'
            )
            :
            '';
        $cookie = Cookie::create(
            $name,
            $value,
            $options
        );

        return $cookie;
    }
}
