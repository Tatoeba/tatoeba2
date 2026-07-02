<?php
namespace App\Middleware;

use App\Model\Entity\User;
use Authentication\Authenticator\FormAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Automatically logs out accounts having role set to spammer or inactive.
 */
class AutoLogoutMiddleware implements MiddlewareInterface
{
    private function checkAutoLogout($service)
    {
        $autoLogout = false;
        $autoLogoutMsg = null;
        $identity = $service->getIdentity();
        if ($identity) {
            $justLoggedIn = $service->getAuthenticationProvider() instanceof FormAuthenticator;
            $role = $identity->get('role');
            if ($role == User::ROLE_SPAMMER) {
                if ($justLoggedIn) {
                    $autoLogoutMsg = __(
                        'This account has been marked as a spammer. '.
                        'You cannot log in with it anymore. '.
                        'Please contact an admin if this is a mistake.'
                    );
                } else {
                    $autoLogoutMsg = __('Your account has been suspended.');
                }
                $autoLogout = true;
            } elseif ($role == User::ROLE_INACTIVE) {
                if ($justLoggedIn) {
                    $autoLogoutMsg = __(
                        'This account has been marked inactive. '.
                        'You cannot log in with it anymore. '.
                        'Please contact an admin if this is a mistake.'
                    );
                } else {
                    $autoLogoutMsg = __('Your account has been deactivated.');
                }
                $autoLogout = true;
            }
        }
        return [$autoLogout, $autoLogoutMsg];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $service = $request->getAttribute('authentication');
        [$autoLogout, $autoLogoutMsg] = $this->checkAutoLogout($service);

        if ($autoLogout) {
            $request = $request->withAttribute($service->getIdentityAttribute(), null);
            $request->getFlash()->set($autoLogoutMsg);
        }

        $response = $handler->handle($request);

        if ($autoLogout) {
            // remove session too
            $return = $service->clearIdentity($request, $response);
            $response = $return['response'];
        }

        return $response;
    }
}
