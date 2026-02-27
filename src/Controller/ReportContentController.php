<?php

namespace App\Controller;

use App\Model\CurrentUser;
use App\Model\ContentReport;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

class ReportContentController extends AppController
{
    public $components = ['Flash'];

    private function process_report($entity)
    {
        $details = $this->request->getData('details', '');
        $origin = $this->request->getQuery('origin', $this->referer());

        if ($this->request->is('post')) {
            $report = new ContentReport(
                CurrentUser::get('username'),
                $entity,
                $details
            );
            if ($report->send()) {
                $this->Flash->set(__(
                    'Thank you for your help. Your report was sent to the admins. '.
                    'If needed, they will reply to you by private message.'
                ));
                return $this->redirect($origin ?? '/');
            } else {
                $email = Configure::read('Tatoeba.communityModeratorEmail');
                $email = "<a href=\"mailto:$email\">$email</a>";
                $this->Flash->set(format(
                    __('Sorry, we were unable to send your report. Please '.
                       'try again later, or instead contact {email}.'),
                    compact('email')
                ));
            }
        }

        $this->set(compact('entity', 'details', 'origin'));
        return $this->render('report');
    }

    public function wall_post($msgId)
    {
        try {
            $this->loadModel('Wall');
            $wallPost = $this->Wall->getMessage($msgId);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException();
        }

        return $this->process_report($wallPost);
    }

    public function sentence_comment($id)
    {
        try {
            $this->loadModel('SentenceComments');
            $comment = $this->SentenceComments
                ->findById($id)
                ->contain(['Users' => function ($q) {
                    return $q->select(['id', 'username', 'image']);
                }])
                ->firstOrFail();
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException();
        }

        return $this->process_report($comment);
    }
}
