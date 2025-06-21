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

    public function wall_post($msgId)
    {
        try {
            $this->loadModel('Wall');
            $entity = $this->Wall->getMessage($msgId);
        } catch (RecordNotFoundException $e) {
            throw new NotFoundException();
        }

        $details = $this->request->getData('details', '');
        $origin = $this->request->getData('origin', $this->referer());

        if ($this->request->is('post')) {
            $report = new ContentReport(
                CurrentUser::get('username'),
                $entity,
                $details,
                Configure::read('Tatoeba.devStylesheet')
            );
            if ($report->send()) {
                $this->Flash->set(__(
                    'Thank you for your help, your report was sent to admins. '.
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
}
