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
 * @link     http://tatoeba.org
 */


/**
 * Controller for private messages.
 *
 * @category API
 * @package  Controllers
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class PrivateMessagesController extends AppController
{
    public $name = 'PrivateMessages';

    public $helpers = array('Html', 'Date');

    public $components = array ('Mailer');


    /**
     * We don't use index at all : by default, we just display the
     * inbox folder to the user
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(array('action' => 'folder', 'Inbox'));
    }

    /**
     * Function which will display the folders to the user.
     * The folder name is given in parameters, as messages are stored by
     * folder name in the database (SQL ENUM)
     *
     * @param string $folder The folder we want to display
     * @param string $status 'all', 'read', 'unread'.
     *
     * @return void
     */
    public function folder($folder = 'Inbox', $status = 'all')
    {
        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'Messages';

        $folder = Sanitize::paranoid($folder);

        $currentUserId = $this->Auth->user('id');

        $conditions = array('folder' => $folder);

        if ($folder == 'Inbox') {
            $conditions['recpt'] = $currentUserId;
        } else if ($folder == 'Sent' || $folder == 'Drafts') {
            $conditions['sender'] = $currentUserId;
        } else if ($folder == 'Trash') {
            $conditions['user_id'] = $currentUserId;
        }

        if ($status == 'read') {
            $conditions['isnonread'] = 0;
        } else if ($status == 'unread') {
            $conditions['isnonread'] = 1;
        }

        $this->paginate = array(
            'PrivateMessage' => array(
                'conditions' => $conditions,
                'contain' => array(
                    'Sender' => array(
                        'fields' => array(
                            'id',
                            'username',
                            'image',
                        )
                    ),
                    'Recipient' => array(
                        'fields' => array(
                            'id',
                            'username',
                            'image',
                        )
                    )
                ),
                'order' => 'date DESC',
                'limit' => 20
            )
        );

        $content = $this->paginate();

        if ($folder == 'Trash') {
            $content = array_map([$this, '_setOriginFolder'], $content);
        }

        $this->set('folder', $folder);
        $this->set('content', $content);
    }

    /**
     * Set the origin folder on the message array.
     *
     * @param array $message
     *
     * @return  array
     */
    private function _setOriginFolder($message)
    {
        $message['PrivateMessage']['origin'] = $this->_findOriginFolder($message);

        return $message;
    }

    /**
     * This function has to send the message, then to display the sent folder
     *
     * @return void
     */
    public function send()
    {
        if ($this->_isIncompleteDraft($this->data['PrivateMessage'])) {
            $this->_redirectIncomplete(
                'You must fill at least the content field.'
            );
        } elseif ($this->_isIncomplete($this->data['PrivateMessage'])) {
            $this->_redirectIncomplete(
                'You must fill at least the "To" field and the content field.'
            );
        }

        $currentUserId = $this->Auth->user('id');

        //Remember new users are not allowed to send more than 5 messages per day
        $messagesTodayOfUser
            = $this->PrivateMessage->messagesTodayOfUser($currentUserId);
        if (CurrentUser::isNewUser() && $messagesTodayOfUser >= 5) {
            $this->Session->setFlash(
                __(
                    "You have reached your message limit for today. ".
                    "Please wait until you can send more messages. ".
                    "If you have received this message in error, ".
                    "please contact administrators at ".
                    "team@tatoeba.org.",
                    true
                )
            );
            $this->redirect(array('action' => 'folder', 'Sent'));
        }

        $now = date("Y/m/d H:i:s", time());

        if ($this->data['PrivateMessage']['submitType'] === 'saveDraft') {
            $this->PrivateMessage->saveDraft(
                $currentUserId,
                $now,
                $this->data
            );

            $this->redirect(array('action' => 'folder', 'Drafts'));
        } else {
            $messageToSend = array(
                'sender'    => $currentUserId,
                'date'      => $now,
                'folder'    => 'Inbox',
                'title'     => $this->data['PrivateMessage']['title'],
                'content'   => $this->data['PrivateMessage']['content'],
                'isnonread' => 1,
            );

            $messageToSave = array_merge($messageToSend, array(
                'user_id'   => $currentUserId,
                'folder'    => 'Sent',
                'isnonread' => 0,
            ));
        }

        if ($this->data['PrivateMessage']['messageId']) {
            $messageToSend['id'] = $this->data['PrivateMessage']['messageId'];
        }

        $recptArray = explode(',', $this->data['PrivateMessage']['recpt']);

        $recptArray = array_map('trim', $recptArray);

        $recptArray = array_unique($recptArray, SORT_REGULAR);

        // loop to send msg to different dest.
        foreach ($recptArray as $recpt) {
            $recptId = $this->PrivateMessage->User->getIdFromUsername($recpt);
            $recptSettings = $this->PrivateMessage->User->getSettings($recptId);

            // we send the msg only if the user exists.
            if ($recptId) {
                $message = $messageToSend;
                $message['recpt'] = $recptId;
                $message['user_id'] = $recptId;
                $message['draft_recpts'] = '';
                $message['sent'] = 1;
                $this->PrivateMessage->save($message);
                if ($recptSettings['User']['send_notifications']) {
                    $this->Mailer->sendPmNotification(
                        $message, $this->PrivateMessage->id
                    );
                }
                $this->PrivateMessage->id = null;

                // we need to save the msg to our outbox folder of course.
                $message = $messageToSave;
                $message['recpt'] = $recptId;
                $message['draft_recpts'] = '';
                $message['sent'] = 1;
                $this->PrivateMessage->save($message);
                $this->PrivateMessage->id = null;
            } else {
                $this->Session->write('unsent_message', $messageToSend);

                $this->Session->setFlash(
                    format(
                        __(
                            'The user {username} to whom you want to send this message '.
                            'does not exist. Please try with another '.
                            'username.',
                            true
                        ),
                        array('username' => $recpt)
                    )
                );
                $this->redirect(array('action' => 'write'));
            }
        }
        $this->redirect(array('action' => 'folder', 'Sent'));
    }

    /**
     * Private message is draft and content is empty.
     *
     * @param  array  $message [Private message submitted by user]
     *
     * @return boolean
     */
    private function _isIncompleteDraft($message)
    {
        return empty($message['content'])
            && $message['submitType'] == 'saveDraft';
    }

    /**
     * Private message recipient or content are empty and message is not draft.
     *
     * @param  array  $message [Private message submitted by user]
     *
     * @return boolean
     */
    private function _isIncomplete($message)
    {
        return (empty($message['recpt']) || empty($message['content']))
            && $message['submitType'] != 'saveDraft';
    }

    /**
     * Set flash message with error and redirect back to write.
     *
     * @param  string $error [Flash message to set]
     */
    private function _redirectIncomplete($error)
    {
        $this->Session->setFlash(
            __($error, true)
        );

        $this->redirect(array('action' => 'write'));
    }

    /**
     * Function to show the content of a message
     *
     * @param int $messageId The identifiers of the message we want to read
     *
     * @return void
     */
    public function show($messageId)
    {
        $this->helpers[] = 'Messages';
        $this->helpers[] = 'PrivateMessages';

        $messageId = Sanitize::paranoid($messageId);
        $pm = $this->PrivateMessage->getMessageWithId($messageId);

        // Redirection to Inbox if the user tries to view a messages that
        // is not theirs.
        $recipientId = $pm['PrivateMessage']['recpt'];
        $senderId = $pm['PrivateMessage']['sender'];
        $currentUserId = CurrentUser::get('id');

        if ($recipientId != $currentUserId && $senderId != $currentUserId) {
            $this->redirect(
                array(
                    'action' => 'folder',
                    'Inbox'
                )
            );
        }

        // Setting message as read
        if ($pm['PrivateMessage']['isnonread'] == 1) {
            $pm['PrivateMessage']['isnonread'] = 0;
            $this->PrivateMessage->save($pm);
        }

        $folder =  $pm['PrivateMessage']['folder'];
        $title = $pm['PrivateMessage']['title'];
        $message = $this->_getMessageFromPm($pm['PrivateMessage']);
        $author = $pm['Sender'];
        $messageMenu = $this->_getMenu($folder, $messageId);

        $this->set('messageMenu', $messageMenu);
        $this->set('title', $title);
        $this->set('author', $author);
        $this->set('message', $message);
        $this->set('folder', $folder);
    }

    /**
     * @param array $privateMessage Private message info.
     *
     * @return array
     */
    private function _getMessageFromPm($privateMessage)
    {
        $message['created'] = $privateMessage['date'];
        $message['text'] = $privateMessage['content'];

        return $message;
    }


    /**
     *
     * @param string $folder    Folder name: Inbox, Sent or Trash
     * @param int    $messageId Id of private message.
     *
     * @return array
     */
    private function _getMenu($folder, $messageId)
    {
        $menu = array();

        if ($folder == 'Trash') {
            $menu[] = array(
                'text' => __('restore', true),
                'url' => array(
                    'action' => 'restore',
                    $messageId
                )
            );

            $menu[] = array(
                'text' => __('permanently delete', true),
                'url' => array(
                    'action' => 'delete',
                    $messageId
                ),
                'confirm' => __('Are you sure?', true)
            );
        } else {
            $menu[] = array(
                'text' => __('delete', true), 
                'url' => array(
                    'action' => 'delete',
                    $messageId
                )
            );
        }
        
        if ($folder == 'Inbox') {
            $menu[] = array(
                'text' => __('mark as unread', true), 
                'url' => array(
                    'action' => 'mark',
                    'Inbox',
                    $messageId
                )
            );
                        
            $menu[] = array(
                'text' => __('reply', true), 
                'url' => '#reply'
            );
        }

        return $menu;
    }

    /**
     * Empty folder
     *
     * @param string $folder  The name of the folder to empty
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
            $this->PrivateMessage->deleteAll($conditions, false);
            $this->Session->setFlash(
                format(
                    __('Folder "{name}" emptied.', true),
                    array('name' => $folder)
                )
            );
        }
        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Delete message function
     *
     * @param int $messageId The identifier of the message we want to delete
     *
     * @return void
     */
    public function delete($messageId)
    {
        $messageId = Sanitize::paranoid($messageId);
        $message = $this->PrivateMessage->findById($messageId);

        if ($message['PrivateMessage']['user_id'] == CurrentUser::get('id')) {
            $deleteForever = $message['PrivateMessage']['folder'] == 'Trash';
            if ($deleteForever) {
                $this->PrivateMessage->delete($messageId);
            } else {
                $message['PrivateMessage']['folder'] = 'Trash';
                $this->PrivateMessage->save($message);
            }
        }

        $this->redirect($this->referer());
    }

    /**
     * Restore message function
     *
     * @param int $messageId The identifier of the message we want to restore
     *
     * @return void
     */
    public function restore($messageId)
    {
        $messageId = Sanitize::paranoid($messageId);

        $message = $this->PrivateMessage->findById($messageId);

        $folder = $this->_findOriginFolder($message);

        $message['PrivateMessage']['folder'] = $folder;

        $this->PrivateMessage->save($message);

        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Determine which folder trash messages originally belonged to.
     *
     * @param  array $message
     *
     * @return string
     */
    private function _findOriginFolder($message)
    {
        if ($message['PrivateMessage']['recpt'] == $this->Auth->user('id')) {
            return 'Inbox';
        } elseif ($message['PrivateMessage']['sent'] == false) {
            return 'Drafts';
        }

        return 'Sent';
    }

    /**
     * Generalistic read/unread marker function.
     *
     * @param string $folderId  The folder identifier where we are while
     * marking this message
     * @param int    $messageId The identifier of the message we want to mark
     *
     * @return void
     */
    public function mark($folderId, $messageId)
    {
        $messageId = Sanitize:: paranoid($messageId);

        $message = $this->PrivateMessage->findById($messageId);
        switch ($message['PrivateMessage']['isnonread']) {
            case 1 : $message['PrivateMessage']['isnonread'] = 0;
                break;
            case 0 : $message['PrivateMessage']['isnonread'] = 1;
                break;
        }
        $this->PrivateMessage->save($message);
        $this->redirect(array('action' => 'folder', $folderId));
    }

    /**
     * Create a new message
     *
     * @param string $recipients The login, or the string containing various login
     *                           separated by a comma, to which we have to send the
     *                           message.
     * @param  int $messageId
     *
     * @return void
     */
    public function write($recipients = null, $messageId = null)
    {
        $this->helpers[] = "PrivateMessages";

        $recoveredMessage = $this->Session->read('unsent_message');

        $userId = CurrentUser::get('id');
        $isNewUser = CurrentUser::isNewUser();

        //For new users, check how many messages they have sent in the last 24hrs
        $canSend = true;
        $messagesToday = 0;
        if ($isNewUser) {
            $messagesToday = $this->PrivateMessage->messagesTodayOfUser($userId);
            $canSend = $messagesToday < 5;
        }

        if ($recipients == null) {
            $recipients = '';
        }

        $this->set('messagesToday', $messagesToday);
        $this->set('canSend', $canSend);
        $this->set('isNewUser', $isNewUser);

        if ($messageId) {
            $messageId = Sanitize::paranoid($messageId);

            $pm = $this->PrivateMessage->getMessageWithId($messageId);

            $senderId = $pm['PrivateMessage']['sender'];

            if ($senderId != $userId) {
                $this->redirect(
                    array(
                        'action' => 'folder',
                        'Drafts'
                    )
                );
            }

            $this->set('recipients', $pm['PrivateMessage']['draft_recpts']);
            $this->set('title', $pm['PrivateMessage']['title']);
            $this->set('content', $pm['PrivateMessage']['content']);
            $this->set('messageId', $messageId);
        } else if ($recoveredMessage) {
            $this->Session->delete('unsent_message');

            $this->set('recipients', $recipients);
            $this->set('title', $recoveredMessage['title']);
            $this->set('content', $recoveredMessage['content']);
            $this->set('hasRecoveredMessage', true);
        } else {
            $this->set('recipients', $recipients);
        }
    }

    /**
     * function called to add a list to a pm
     *
     * @param string $type         The type of object to join to the message
     * @param int    $joinObjectId The identifier of the object to join
     *
     * @return void
     */
    public function join($type = null, $joinObjectId = null)
    {
        $type = Sanitize::paranoid($type);
        $joinObjectId = Sanitize::paranoid($joinObjectId);

        if ($type != null && $joinObjectId != null) {
            $this->params['action'] = 'write';
            $this->set('msgPreContent', '['.$type.':'.$joinObjectId.']');
            $this->write();
        } else {
            $this->redirect(array('action' => 'write'));
        }
    }
}
?>
