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

    public function index()
    {
        $docurl = [
            'version' => 'v1',
            'controller' => 'doc',
            'action' => 'show',
        ];
        $specurl = $this->getOpenapiSpec('v1');
        $this->set(compact('docurl', 'specurl'));
    }

    private function getOpenapiSpec($version)
    {
        $specFilename = "openapi-$version.json";
        $specFile = new File(WWW_ROOT . 'api' . DS . $specFilename);
        if ($specFile->exists()) {
            return "/$specFilename";
        } else {
            throw new NotFoundException("Unknown API version code: $version");
        }
    }

    public function show()
    {
        $version = $this->getRequest()->getParam('version');
        $specurl = $this->getOpenapiSpec($version);
        $this->set(compact('specurl', 'version'));
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
