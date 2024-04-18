<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Event\Event;

class ExportsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $this->Security->setConfig('unlockedActions', [
            'add',
        ]);
        $this->Auth->allowedActions = ['add', 'get', 'download'];

        $this->loadComponent('RequestHandler');

        return parent::beforeFilter($event);
    }

    public function add()
    {
        $export = false;
        if ($this->request->is('post')) {
            $export = $this->Exports->createExport(
                CurrentUser::get('id'),
                $this->request->getData()
            );
        }

        if ($export) {
            $this->set(compact('export'));
            $this->set('_serialize', ['export']);
            $this->RequestHandler->renderAs($this, 'json');
        }
    }

    public function get($id)
    {
        $export = $this->Exports->get($id);

        if ($export) {
            $this->set(compact('export'));
            $this->set('_serialize', ['export']);
            $this->RequestHandler->renderAs($this, 'json');
        }
    }

    public function download($exportId)
    {
        try {
            $export = $this->Exports->get($exportId);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        if ($export->user_id !== CurrentUser::get('id')) {
            throw new \Cake\Http\Exception\ForbiddenException();
        } elseif ($export->status != 'online') {
            throw new \Cake\Http\Exception\NotFoundException();
        } else {
            $this->autoRender = false;
            return $this->getResponse()
                        ->withFile($export->filename, ['download' => true])
                        /* withFile() sets Content-Disposition, but we don't
                           need it since the URL has the filename at the end */
                        ->withoutHeader('Content-Disposition')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withStringBody('')
                        ->withHeader('X-Accel-Redirect', $export->url);
        }
    }
}
