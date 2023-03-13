<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;

class ApiController extends Controller
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function beforeFilter(Event $event)
    {
        if ($this->getRequest()->getParam('version') != 'unstable') {
            return $this->default();
        }
    }

    public function default()
    {
        $this->autoRender = false;
        return $this
            ->getResponse()
            ->withStatus(404);
    }
}
