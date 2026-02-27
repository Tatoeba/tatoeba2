<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

class DocController extends Controller
{
    public function initialize()
    {
        parent::initialize();

        // assign 'title' block from view var $title, if set
        // or default to 'Tatoeba API'
        $this->getEventManager()->attach(
            function (Event $event) {
                $view = $event->subject;
                $view->assign('title', $view->get('title') ?? 'Tatoeba API');
            },
            'View.beforeRender'
        );
    }

    public function beforeFilter(Event $event)
    {
        $docurl = [
            'controller' => 'doc',
            'action' => 'show',
        ];
        $specurl = $this->getOpenapiSpec();
        $this->set(compact('docurl', 'specurl'));
    }

    public function index()
    {
    }

    private function getOpenapiSpec()
    {
        $specFilename = "openapi.json";
        $specFile = new File(WWW_ROOT . 'api' . DS . $specFilename);
        if ($specFile->exists()) {
            return "/$specFilename";
        } else {
            throw new NotFoundException("Unknown API version code: $version");
        }
    }

    public function show()
    {
    }

    public function examples()
    {
        $this->set('title', 'Tatoeba API examples');
        $name = $this->getRequest()->getParam('name');
        try {
            $this->render("examples/$name");
        } catch (MissingTemplateException $e) {
            throw new NotFoundException();
        }
    }
}
