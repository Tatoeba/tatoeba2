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

App::uses('AppController', 'Controller');

class LicensingController extends AppController {

    public $uses = array(
        'Queue.QueuedTask',
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Security->unlockedActions = array(
            'switch_my_sentences',
        );
    }

    public function switch_my_sentences() {
        $currentUserId = CurrentUser::get('id');

        $currentJob = $this->QueuedTask->find('first', array(
            'conditions' => array(
                'jobtype' => 'SwitchSentencesLicense',
                'group' => $currentUserId,
            )
        ));
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
                $currentJob = $this->QueuedTask->createJob(
                    'SwitchSentencesLicense',
                    $options,
                    null,
                    $currentUserId
                );
                if ($currentJob) {
                    $currentJob = $this->QueuedTask->read();
                }
            }
        }

        $this->set(compact('currentJob'));
    }
}
