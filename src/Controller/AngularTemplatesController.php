<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class AngularTemplatesController extends AppController
{
    public $name = 'AngularTemplates';

    public function beforeRender(Event $event)
    {
        $this->viewBuilder()->enableAutoLayout(false);
        return parent::beforeRender($event);
    }

    public function interface_language()
    {
    }
}
