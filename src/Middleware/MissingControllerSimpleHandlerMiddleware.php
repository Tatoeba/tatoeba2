<?php

namespace App\Middleware;

use Cake\Routing\Exception\MissingControllerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MissingControllerSimpleHandlerMiddleware
{
    private $errorMessage;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        try {
            return $next($request, $response);
        } catch (MissingControllerException $exception) {
            return $response->withStatus(400, $this->errorMessage);
        }
    }
}
