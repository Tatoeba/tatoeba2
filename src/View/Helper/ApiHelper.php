<?php
namespace App\View\Helper;

use Cake\Routing\Router;
use Cake\View\Helper;

class ApiHelper extends Helper
{
    public function getTatoebaUrl() {
        $request = Router::getRequest();
        if ($request) {
            $host = explode('.', $request->host());
            $host = array_slice($host, 1);
            $host = implode('.', $host);
            $scheme = $request->scheme();
            return "$scheme://$host";
        } else {
            return null;
        }
    }
}
