<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2014  Allan SIMON <allan.simon@supinfo.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Event\NotificationListener;
use Cake\Event\Event;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Controller for the wall.
 *
 * @category Wall
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

class WallController extends AppController
{
    public $name = 'Wall' ;
    public $paginate = [
        'sortWhitelist' => ['WallThreads.last_message_date'],
        'order' => ['WallThreads.last_message_date' => 'DESC'],
        'limit' => 10,
        'fields' => ['lft', 'rght'],
        'conditions' => ['Wall.parent_id IS NULL'],
        'contain' => [
            'WallThreads' => ['fields' => ['last_message_date']]
        ]
    ];
    public $helpers = array(
        'Wall',
        'Date',
        'Pagination'
    );
    public $components = array('Flash');
    /**
     * to know who can do what
     *
     * @return void
     */

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save_inside',
        ]);
        
        $eventManager = $this->Wall->getEventManager();
        $eventManager->attach(new NotificationListener());

        return parent::beforeFilter($event);
    }

    /**
     * display main wall page with all messages
     *
     * @return void
     */
    public function index()
    {
        $tenLastMessages = $this->Wall->getLastMessages(10);

        $userId = $this->Auth->user('id');

        try {
            $messageLftRght = $this->paginate();
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }
        $messages = $this->Wall->getMessagesThreaded($messageLftRght);
        $messages = $this->Permissions->getWallMessagesOptions(
            $messages,
            $userId
        );

        $isAuthenticated = !empty($userId);

        $this->set('isAuthenticated', $isAuthenticated);
        $this->set('allMessages', $messages);
        $this->set('tenLastMessages', $tenLastMessages);
    }


    /**
     * save a new first message
     *
     * @return void
     */
    public function save()
    {
        if ($this->Auth->user('id')) {
            $content = $this->request->getData('content');
            $session = $this->request->getSession();
            $lastMess = $session->read('hash_last_wall');
            $thisMess = md5($content);
            
            $session->write(
                'hash_last_wall',
                $thisMess
            );
            if ($lastMess != $thisMess) {
                $newPost = $this->Wall->newEntity([
                    'owner'   => $this->Auth->user('id'),
                    'content' => $content,
                ]);
                // now save to database
                $this->Wall->save($newPost);
            }
        }

        $this->redirect(
            array('action'=>'index')
        );
    }

    /**
     * save a new reply
     *
     * @return void
     */

    public function save_inside()
    {
        $data = $this->request->getData();
        $userId = $this->Auth->user('id');
        $content = $data['content'];
        $parentId = $data['replyTo'];

        // now save to database
        $message = $this->Wall->saveReply($parentId, $content, $userId);
        $this->set('message', $message);
        $this->viewBuilder()->setLayout('json');
    }

    /**
     * Edit a wall post
     *
     * @param int $messageId Id of the message to edit
     *
     * @return void
     */
    public function edit($messageId)
    {
        try {
            $message = $this->Wall->get($messageId, ['contain' => 'Users']);
        } catch(RecordNotFoundException $e) {
            return $this->redirect($this->referer());
        }

        $messagePermissions = $this->Permissions->getWallMessageOptions(
            false,
            $message,
            CurrentUser::get('id')
        );
        if ($messagePermissions['canEdit'] == false) {
            return $this->_cannotEdit();
        }
        
        if ($this->request->is('put')) {
            $data = $this->request->getData();
            $this->Wall->patchEntity($message, [
                'content' => $data['content']
            ]);
            $savedMessage = $this->Wall->save($message);
            if ($savedMessage) {
                $this->Flash->set(__('Message saved.'));
                $this->redirect([
                    'action' => 'show_message',
                    $messageId,
                    '#' => "message_$messageId"
                ]);
            } else if ($message->getErrors()) {
                $firstValidationErrorMessage = reset($message->getErrors())[0];
                $this->Flash->set($firstValidationErrorMessage);
                $this->redirect(['action' => 'edit', $messageId]);
            }
        } else { 
            $this->set('message', $message);
        }
    }

    private function _cannotEdit() {
        $noPermission = __(
            'You do not have permission to edit this message.', true
        );
        $contactAdmin = format(__(
            'If you have received this message in error, '.
            'please contact administrators at {email}.'
        ), array('email' => 'team@tatoeba.org'));

        $this->Flash->set(
            '<p>'.$noPermission.'</p>'.
            '<p>'.$contactAdmin.'</p>'
        );
        return $this->redirect(array('action' => 'index'));
    }


    /**
     * use to delete a given message on the wall
     *
     * @param int $messageId Id of the message to delete
     *
     * @return void
     */

    public function delete_message($messageId)
    {
        $deleted = $this->Wall->deleteMessage($messageId);
        $this->redirect($this->referer());
    }

    /**
     * Use to display a single thread
     * usefull when we want a permalink to a message with its whole thread
     *
     * @param int $messageId the message to display with its thread
     *
     * @return void
     */
    public function show_message($messageId)
    {
        $userId = $this->Auth->user('id');

        $thread = $this->Wall->getWholeThreadContaining($messageId);

        /* NOTE : have a link to point the thread within the other thread
           is virtually impossible, as the ordering can change between the page
           generation and the user click on the link
        */
        $thread = $this->Permissions->getWallMessagesOptions(
            $thread,
            $userId
        );

        if (!empty($thread)) {
            $this->set("message", $thread[0]);
        } else {
            $this->Flash->set(
                __('The message you are trying to view does not exist or has been deleted.')
            );
            $this->redirect(
                array('action' => 'index')
            );
        }


        $this->set("isAuthenticated", $this->Auth->user());
    }


    /**
     * Display messages of a user.
     *
     * @param string $username Username.
     *
     * @return void
     */
    public function messages_of_user($username)
    {
        $userId = $this->Wall->Users->getIdFromUsername($username);

        if (isset($userId)) {
            $this->paginate = [
                'order' => ['date' => 'DESC'],
                'limit' => 20,
                'fields' => [
                    'id', 'date', 'content', 'hidden', 'owner', 'modified'
                ],
                'conditions' => [
                    'owner' => $userId,
                ],
                'contain' => [
                    'Users' => [
                        'fields' => ['username', 'image']
                    ]
                ]
            ];

            $messages = $this->paginate();
        }

        $this->set('userExists', (bool) $userId);
        $this->set('messages', $messages ?? null);
        $this->set('username', $username);
    }


    /**
     * Hides a given message on the Wall. The message is still going to be there
     * but only visible to the admins and the author of the message.
     *
     * @param int $messageId Id of the message to hide
     *
     * @return void
     */
    public function hide_message($messageId)
    {
        if (CurrentUser::isAdmin()) {
            $message = $this->Wall->get($messageId);
            $message->hidden = true;
            $this->Wall->save($message);

            // redirect to previous page
            $this->redirect($this->referer());
        }

    }


    /**
     * Display back a given message on the Wall that was hidden.
     *
     * @param int $messageId Id of the message to display again
     *
     * @return void
     */
    public function unhide_message($messageId)
    {
        if (CurrentUser::isAdmin()) {
            $message = $this->Wall->get($messageId);
            $message->hidden = false;
            $this->Wall->save($message);

            // redirect to previous page
            $this->redirect($this->referer());
        }

    }

}
