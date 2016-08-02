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
     * Display the inbox folder to the user.
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(array('action' => 'folder', 'Inbox'));
    }

    /**
     * Display folder to the user. Messages are stored by folder name in the
     * database (SQL ENUM).
     *
     * @param string $folder [The folder to display.]
     * @param string $status [Message status: 'all', 'read', 'unread']
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
     * @param array $message [Private message array.]
     *
     * @return array
     */
    private function _setOriginFolder($message)
    {
        $message['PrivateMessage']['origin'] = $this->_findOriginFolder($message);

        return $message;
    }

    /**
     * Send the message, redirect to the sent folder.
     *
     * @return void
     */
    public function send()
    {
        $this->_validateMessage();

        $currentUserId = $this->Auth->user('id');

        $now = date("Y/m/d H:i:s", time());

        if ($this->data['PrivateMessage']['submitType'] === 'saveDraft') {
            $this->PrivateMessage->saveDraft($currentUserId, $now, $this->data);

            $this->redirect(array('action' => 'folder', 'Drafts'));
        }

        $toSend = $this->_buildMessage($currentUserId, $now);

        $recipients = $this->_buildRecipientsArray();

        $sentToday = $this->PrivateMessage->messagesTodayOfUser($currentUserId);

        foreach ($recipients as $recpt) {
            if (CurrentUser::isNewUser() && $sentToday >= 5) {
                $this->_redirectDailyLimitReached();
            }

            $recptId = $this->PrivateMessage->User->getIdFromUsername($recpt);

            if (!$recptId) {
                $this->_redirectInvalidUser($toSend, $recpt);
            }

            $message = $this->PrivateMessage->saveToInbox($toSend, $recptId);

            $this->_sendMessageNotification($message, $recptId);

            $this->PrivateMessage->id = null;

            $this->PrivateMessage->saveToOutbox(
                $toSend,
                $recptId,
                $currentUserId
            );

            $this->PrivateMessage->id = null;

            $sentToday += 1;
        }

        $this->redirect(array('action' => 'folder', 'Sent'));
    }

    /**
     * Validate the message.
     *
     * @return void
     */
    private function _validateMessage()
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
    }

    /**
     * Private message is draft and content is empty.
     *
     * @param  array $message [Private message submitted by user.]
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
     * @param  array $message [Private message submitted by user.]
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
     * @param  string $error [Flash message to set.]
     *
     * @return void
     */
    private function _redirectIncomplete($error)
    {
        $this->Session->setFlash(
            __($error, true)
        );

        $this->redirect(array('action' => 'write'));
    }

    /**
     * Build message to send.
     *
     * @param  int    $currentUserId [ID of current user.]
     * @param  string $now           [Current timestamp.]
     *
     * @return array
     */
    private function _buildMessage($currentUserId, $now)
    {
        $message = array(
            'sender'    => $currentUserId,
            'date'      => $now,
            'folder'    => 'Inbox',
            'title'     => $this->data['PrivateMessage']['title'],
            'content'   => $this->data['PrivateMessage']['content'],
            'isnonread' => 1,
        );

        if ($this->data['PrivateMessage']['messageId']) {
            $message['id'] = $this->data['PrivateMessage']['messageId'];
        }

        return $message;
    }

    /**
     * Build array of recipients from recipents string.
     *
     * @return array
     */
    private function _buildRecipientsArray()
    {
        $recptArray = explode(',', $this->data['PrivateMessage']['recpt']);

        $recptArray = array_map('trim', $recptArray);

        return array_unique($recptArray, SORT_REGULAR);
    }

    /**
     * Set flash message and redirect if new user has sent too many messages.
     *
     * @return void
     */
    private function _redirectDailyLimitReached()
    {
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

    /**
     * Set flash message and redirect if user is invalid.
     *
     * @param  array  $message [Message array.]
     * @param  string $recpt   [Recipient username.]
     *
     * @return void
     */
    private function _redirectInvalidUser($message, $recpt)
    {
        $this->Session->write('unsent_message', $message);

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

    /**
     * Send email notification if user setting allows.
     *
     * @param  array $message [Private message.]
     * @param  int   $userId  [ID of user to send notification to.]
     *
     * @return void
     */
    private function _sendMessageNotification($message, $userId)
    {
        $userSettings = $this->PrivateMessage->User->getSettings($userId);

        if ($userSettings['User']['send_notifications']) {
            $this->Mailer->sendPmNotification(
                $message,
                $this->PrivateMessage->id
            );
        }
    }

    /**
     * Show a message.
     *
     * @param  int $messageId [ID of message to show.]
     *
     * @return void
     */
    public function show($messageId)
    {
        $this->helpers[] = 'Messages';
        $this->helpers[] = 'PrivateMessages';

        $messageId = Sanitize::paranoid($messageId);
        $privateMessage = $this->PrivateMessage->getMessageWithId($messageId);

        $this->_redirectMessageNotUsers($privateMessage);

        $privateMessage = $this->PrivateMessage->markAsRead($privateMessage);

        $author = $privateMessage['Sender'];
        $folder =  $privateMessage['PrivateMessage']['folder'];
        $message = $this->_getMessageFromPm($privateMessage['PrivateMessage']);
        $messageMenu = $this->_getMenu($folder, $messageId);
        $title = $privateMessage['PrivateMessage']['title'];

        $this->set('author', $author);
        $this->set('folder', $folder);
        $this->set('message', $message);
        $this->set('messageMenu', $messageMenu);
        $this->set('title', $title);
    }

    /**
     * Redirect to Inbox is user tries to view a message that is not theirs.
     *
     * @param  array $message [Private message.]
     *
     * @return void
     */
    private function _redirectMessageNotUsers($message)
    {
        $recipientId = $message['PrivateMessage']['recpt'];
        $senderId = $message['PrivateMessage']['sender'];
        $currentUserId = CurrentUser::get('id');

        if ($recipientId != $currentUserId && $senderId != $currentUserId) {
            $this->redirect(
                array(
                    'action' => 'folder',
                    'Inbox'
                )
            );
        }
    }

    /**
     * Get message details from the private message array.
     *
     * @param array $privateMessage [Private message array.]
     *
     * @return array
     */
    private function _getMessageFromPm($privateMessage)
    {
        return [
            'created' => $privateMessage['date'],
            'text' => $privateMessage['content']
        ];
    }

    /**
     * Get menu for folder.
     *
     * @param string $folder    [Folder name: 'Inbox', 'Sent', 'Trash']
     * @param int    $messageId [Id of private message.]
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
     * Delete all messages in folder.
     *
     * @param string $folder  [Name of the folder to empty.]
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
     * Delete message function.
     *
     * @param int $messageId [ID of message to delete.]
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
     * Restore message from trash to original folder.
     *
     * @param int $messageId [ID of message to restore.]
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
     * Determine which folder trash message originally belonged to.
     *
     * @param  array $message [Private message array.]
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
     * Mark messages as read.
     *
     * @param string $folder    [Folder where action takes place.]
     * @param int    $messageId [ID of message to mark.]
     *
     * @return void
     */
    public function mark($folder, $messageId)
    {
        $messageId = Sanitize::paranoid($messageId);

        $message = $this->PrivateMessage->findById($messageId);

        $this->PrivateMessage->markAsRead($message);

        $this->redirect(array('action' => 'folder', $folder));
    }

    /**
     * Create a new message.
     *
     * @param string $recipients [Username, or comma separated usernames.]
     * @param int $messageId     [ID of message, if exists.]
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
     * Add a list to a private message.
     *
     * @param string $type         [Type of object to join to the message.]
     * @param int    $joinObjectId [ID object to join.]
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
