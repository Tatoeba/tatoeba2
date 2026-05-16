<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.
 */
class ErrorController extends Controller
{
    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        $this->viewBuilder()->setClassName('Json');
    }
}
