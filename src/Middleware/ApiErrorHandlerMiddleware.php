<?php

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Routing\Exception\MissingControllerException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiErrorHandlerMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        try {
            return $next($request, $response);
        } catch (MissingControllerException $exception) {
            return $response->withStatus(400, 'Invalid endpoint');
        } catch (RecordNotFoundException|NotFoundException $exception) {
            if (Configure::read('debug')) {
                throw $exception; // pass on to ErrorHandlerMiddleware
            } else {
                return $response->withStatus(404, 'Not found');
            }
        }
    }
}
