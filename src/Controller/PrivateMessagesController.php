<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 DEPARIS Étienne <etienne.deparis@umaneti.net>
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
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use App\Model\CurrentUser;
use App\Event\NotificationListener;

/**
 * Controller for private messages.
 *
 * @category API
 * @package  Controllers
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class PrivateMessagesController extends AppController
{
    public $name = 'PrivateMessages';
    public $helpers = array('Html', 'Date');
    public $components = array('Flash');

    public function beforeFilter(Event $event)
    {
        $eventManager = $this->PrivateMessages->getEventManager();
        $eventManager->on(new NotificationListener());
        
        return parent::beforeFilter($event);
    }

    /**
     * Display the inbox folder.
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(array('action' => 'folder', 'Inbox'));
    }

    /**
     * Display folder.
     *
     * @param string $folder The folder to display.
     * @param string $status Message status: 'all', 'read', 'unread'
     *
     * @return void
     */
    public function folder($folder = 'Inbox', $status = 'all')
    {
        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'Messages';

        $this->paginate = $this->PrivateMessages->getPaginatedMessages(
            $this->Auth->user('id'),
            $folder,
            $status
        );

        $content = $this->paginate();

        $this->set('folder', $folder);
        $this->set('content', $content);
        $this->set('status', $status);
    }

    /**
     * Send the message, redirect to the sent folder.
     *
     * @return void
     */
    public function send()
    {
        $currentUserId = $this->Auth->user('id');
        $now = date("Y-m-d H:i:s", time());
        $data = $this->request->getData();

        if ($data['submitType'] === 'saveDraft') {
            $this->PrivateMessages->saveDraft($currentUserId, $now, $data);

            $this->redirect(array('action' => 'folder', 'Drafts'));
        } else {
            $hasErrors = false;
            $messages = $this->PrivateMessages->send($currentUserId, $now, $data);
            foreach ($messages as $msg) {
                if (!$msg) {
                    $hasErrors = true;
                } else if (!empty($msg->getErrors())) {
                    $errors = $msg->getErrors();
                    $hasErrors = true;
                    foreach($errors as $error) {
                        $this->Flash->set(reset($error));
                    }
                }
            }
            if ($hasErrors || empty($messages)) {
                $unsentMessage = $data;
                $this->request->getSession()->write('unsent_message', $unsentMessage);
                return $this->redirect(array('action' => 'write'));
            }
            
            $this->redirect(array('action' => 'folder', 'Sent'));
        }
    }

    /**
     * Show a message.
     *
     * @param  int $messageId ID of message to show.
     *
     * @return void
     */
    public function show($messageId)
    {
        $this->helpers[] = 'Messages';
        $this->helpers[] = 'PrivateMessages';

        $message = $this->PrivateMessages->readMessage($messageId);

        if (!$message) {
            return $this->redirect(['action' => 'folder', 'Inbox']);
        }

        $this->set('message', $message);
    }

    /**
     * Delete all messages in folder.
     *
     * @param string $folder Name of the folder to empty.
     *
     * @return void
     */
    public function empty_folder($folder)
    {
        if ($folder == 'Trash') {
            $conditions = array(
                'user_id' => CurrentUser::get('id'),
                'folder' => $folder,
            );

            $this->PrivateMessages->deleteAll($conditions);

            $this->Flash->set(
                format(
                    __('Folder "{name}" emptied.'),
                    array('name' => $folder)
                )
            );
        }

        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Delete message function.
     *
     * @param int $messageId ID of message to delete.
     *
     * @return void
     */
    public function delete($messageId)
    {
        $this->PrivateMessages->deleteMessage($messageId);

        $this->redirect($this->referer());
    }

    /**
     * Restore message from trash to original folder.
     *
     * @param int $messageId ID of message to restore.
     *
     * @return void
     */
    public function restore($messageId)
    {
        $message = $this->PrivateMessages->restoreMessage($messageId);
        $folder = $message ? $message->folder : 'Trash';

        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Toggle message read/unread field.
     *
     * @param string $folder    Folder where action takes place.
     * @param int    $messageId ID of message to mark.
     *
     * @return void
     */
    public function mark($folder, $messageId)
    {
        $this->PrivateMessages->toggleUnread($messageId);

        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Create a new message.
     *
     * @param string $recipients Username, or comma separated usernames.
     * @param int    $messageId  ID of message, if exists.
     *
     * @return void
     */
    public function write($recipients = '', $messageId = null)
    {
        $this->helpers[] = 'PrivateMessages';

        $recoveredMessage = $this->request->getSession()->read('unsent_message');

        $userId = CurrentUser::get('id');

        $messagesToday = $this->PrivateMessages->todaysMessageCount($userId);

        $this->set('messagesToday', $messagesToday);
        $this->set('canSend', $this->PrivateMessages->canSendMessage($userId));
        $this->set('isNewUser', CurrentUser::isNewUser());

        if ($messageId) {
            $pm = $this->PrivateMessages->get($messageId);
            $recipients = $pm->draft_recpts;
            if ($pm->sender != $userId) {
                $this->redirect([
                    'action' => 'folder',
                    'Drafts'
                ]);
            }
        } else if ($recoveredMessage) {
            $pm = $this->PrivateMessages->newEntity($recoveredMessage);
            $this->request->getSession()->delete('unsent_message');
            $this->set('hasRecoveredMessage', true);
        } else {
            $pm = $this->PrivateMessages->newEntity();            
        }
        $this->set('recipients', $recipients);
        $this->set('pm', $pm);
    }
}
