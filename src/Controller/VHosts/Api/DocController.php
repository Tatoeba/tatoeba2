<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;

class DocController extends Controller
{
    public function index()
    {
    }

    public function show()
    {
        $this->set('version', $this->getRequest()->getParam('version'));
    }
}
