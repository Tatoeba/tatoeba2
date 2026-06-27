<?php
declare(strict_types=1);

namespace App\Authenticator;

use Authentication\Authenticator\SessionAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Session Authenticator that does not save password
 */
class SessionWithoutPasswordAuthenticator extends SessionAuthenticator
{
    /**
     * @inheritDoc
     */
    public function persistIdentity(ServerRequestInterface $request, ResponseInterface $response, $identity): array
    {
        if (is_object($identity)) {
            $identity = clone $identity;
        }
        unset($identity['password']);
        return parent::persistIdentity($request, $response, $identity);
    }
}
