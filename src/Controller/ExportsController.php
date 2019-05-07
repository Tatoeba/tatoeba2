<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Event\Event;

class ExportsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'add',
        ]);

        $this->loadComponent('RequestHandler');

        return parent::beforeFilter($event);
    }

    public function index()
    {
        $exports = $this->Exports->getExportsOf(CurrentUser::get('id'));

        $this->set(compact('exports'));

        $this->loadModel('SentencesLists');
        $this->set('searchableLists', $this->SentencesLists->getSearchableLists());
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

    public function download($exportId)
    {
        try {
            $export = $this->Exports->get($exportId);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        if ($export->user_id != CurrentUser::get('id')) {
            throw new \Cake\Http\Exception\ForbiddenException();
        } else {
            return $this->redirect($export->url);
        }
    }
}
