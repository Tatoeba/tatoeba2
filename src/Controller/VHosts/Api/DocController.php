<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;

class DocController extends Controller
{
    public function index()
    {
    }

    private function getOpenapiSpec($version)
    {
        $version = $this->getRequest()->getParam('version');
        $specFilename = "openapi-$version.json";
        $specFile = new File(WWW_ROOT . 'api' . DS . $specFilename);
        if ($specFile->exists()) {
            return "/$specFilename";
        } else {
            throw new NotFoundException();
        }
    }

    public function show()
    {
        $version = $this->getRequest()->getParam('version');
        $specurl = $this->getOpenapiSpec($version);
        $this->set('specurl', $specurl);
    }
}
