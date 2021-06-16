<?php
namespace App\Error;

use Cake\Error\ErrorHandler;
use Cake\Routing\Router;

class TatoebaErrorHandler extends ErrorHandler
{
    protected function _displayError($error, $debug)
    {
        $request = Router::getRequest();

        // Prevent messing up with json output;
        // only log error to debug.log
        if ($request && !$request->accepts('application/json')) {
            parent::_displayError($error, $debug);
        }
    }
}
