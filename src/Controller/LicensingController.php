<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2018 Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Core\Configure;
use Cake\Event\Event;

class LicensingController extends AppController {

    public function beforeFilter(Event $event)
    {
        $this->Security->unlockedActions = array(
            'switch_my_sentences',
        );

        return parent::beforeFilter($event);
    }

    public function switch_my_sentences() {
        $currentUserId = CurrentUser::get('id');

        $this->loadModel('Queue.QueuedJobs');
        $currentJob = $this->QueuedJobs->find()
            ->where([
                'job_type' => 'SwitchSentencesLicense',
                'job_group' => $currentUserId,
            ])
            ->first();

        if ($this->request->is('post')) {
            if ($currentJob) {
                $this->Flash->set(__(
                    'A license switch is already in progress.'
                ));
            } else {
                $options = array(
                    'userId' => $currentUserId,
                    'dryRun' => false,
                    'UIlang' => Configure::read('Config.language'),
                    'sendReport' => true,
                );
                $currentJob = $this->QueuedJobs->createJob(
                    'SwitchSentencesLicense',
                    $options,
                    ['group' => $currentUserId]
                );
            }
        }

        $this->set(compact('currentJob'));
    }
}
