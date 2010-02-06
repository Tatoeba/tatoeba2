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

App::import('Core', 'Sanitize');

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

    public $helpers = array('Comments', 'Languages',
                        'Navigation', 'Html', 'Date');
    public $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');

    public $langs = array('en', 'fr', 'jp', 'es', 'de');

    /**
     * Framework function called to manage access to this controller
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array('check','join');
        /**
         * quick fix because "check" is called in top1.ctp,
         * and if a pending user tries to log in it will
         * not work. "check" is currently defined as
         * accessible only for registered users and above.
         */
    }

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
     * @param string $folderId The folder identifier we want to display
     *
     * @return void
     */
    public function folder($folderId = 'Inbox')
    {

        $inboxes = $this->PrivateMessage->getMessages(
            $folderId,
            $this->Auth->user('id')
        );

        $content = array();

        foreach ($inboxes as $message) {
            $fromUser = new User();
            $fromUser->id = $message['PrivateMessage']['sender'];
            $fromUser = $fromUser->read();


            $toUser = new User();
            $toUser->id = $message['PrivateMessage']['recpt'];
            $toUser = $toUser->read();

            $content[] = array(
                'to'        => $toUser['User']['username'],
                'from'      => $fromUser['User']['username'],
                'title'     => $message['PrivateMessage']['title'],
                'id'        => $message['PrivateMessage']['id'],
                'date'      => $message['PrivateMessage']['date'],
                'isnonread' => $message['PrivateMessage']['isnonread']
            );

        }
        
        $this->set('folder', $folderId);
        $this->set('content', $content);
    }

    /**
     * This function has to send the message, then to display the sent folder
     *
     * @return void
     */
    public function send()
    {
        Sanitize::html($this->data['PrivateMessage']['recpt']);
        // This doesn't work... Why do you have write it ?
        //Sanitize::html($this->data['PrivateMessage']['send']);
        Sanitize::html($this->data['PrivateMessage']['content']);
        Sanitize::html($this->data['PrivateMessage']['title']);

        if (!empty($this->data['PrivateMessage']['recpt'])
            && !empty($this->data['PrivateMessage']['content'])
        ) {
            $this->data['PrivateMessage']['sender']
                = $this->Auth->user('id');

            $recptArray = explode(
                ',',
                $this->data['PrivateMessage']['recpt']
            );

            // loop to send msg to different dest.
            foreach ($recptArray as $recpt) {

                $recpt = trim($recpt);

                /**
                 * I keep the recursive as I disable it, in order to have
                 * only tiny results.
                 */
                $this->PrivateMessage->User->recursive = 0;
                $toUser = $this->PrivateMessage->User->findByUsername($recpt);

                // we send the msg only if the user exists.
                if ($toUser) {

                    $this->data['PrivateMessage']['recpt']
                        = $toUser['User']['id'];
                    $this->data['PrivateMessage']['user_id']
                        = $toUser['User']['id'];
                    $this->data['PrivateMessage']['folder'] = 'Inbox';
                    $this->data['PrivateMessage']['date']
                        = date("Y/m/d H:i:s", time());
                    $this->data['PrivateMessage']['isnonread'] = 1;
                    $this->PrivateMessage->save($this->data);
                    $this->PrivateMessage->id = null;

                    // we need to save the msg to our outbox folder of course.
                    $this->data['PrivateMessage']['user_id']
                        = $this->Auth->user('id');
                    $this->data['PrivateMessage']['folder'] = 'Sent';
                    $this->data['PrivateMessage']['isnonread'] = 0;
                    $this->PrivateMessage->save($this->data);
                    $this->PrivateMessage->id = null;
                } else {
                    $this->Session->setFlash(
                        sprintf(
                            __(
                                'The user %s you to want to send this message
                                to does not exist. Please try with another
                                username.',
                                true
                            ),
                            $recpt
                        )
                    );
                    $this->redirect(array('action' => 'write'));
                }
            }
            $this->redirect(array('action' => 'folder', 'Sent'));
        } else {
            $this->Session->setFlash(
                __(
                    'You must fill at least the "To" field and the content
                    field.',
                    true
                )
            );
            $this->redirect(array('action' => 'write'));
        }
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

        Sanitize::paranoid($messageId);
        /**
         * The following lines of code check if a message is read, or not
         * and change is read value automatically.
         */
        $message = $this->PrivateMessage->findById($messageId);
        if ($message['PrivateMessage']['isnonread'] == 1) {
            $message['PrivateMessage']['isnonread'] = 0;
            $this->PrivateMessage->save($message);
        }

        $toUser = new User();
        $toUser->id = $message['PrivateMessage']['sender'];
        $toUser = $toUser->read();

        $content = array(
            'from' => $toUser['User']['username'],
            'title' => $message['PrivateMessage']['title'],
            'content' => nl2br($message['PrivateMessage']['content']),
            'id' => $message['PrivateMessage']['id'],
            'date' => $message['PrivateMessage']['date'],
            'isnonread' => $message['PrivateMessage']['isnonread'],
            'folder' => $message['PrivateMessage']['folder']
        );

        $this->set('content', $content);

    }

    /**
     * Delete message function
     *
     * @param string $folderId  The folder identifier where we are while
     * deleting this message
     * @param int    $messageId The identifier of the message we want to delete
     *
     * @return void
     */
    public function delete($folderId, $messageId)
    {
        Sanitize:: paranoid($messageId);
        $message = $this->PrivateMessage->findById($messageId);
        $message['PrivateMessage']['folder'] = 'Trash';
        $this->PrivateMessage->save($message);
        $this->redirect(array('action' => 'folder', $folderId));
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

        Sanitize:: paranoid($messageId);
        $message = $this->PrivateMessage->findById($messageId);

        if ($message['PrivateMessage']['recpt'] == $this->Auth->user('id')) {
            $folder = 'Inbox';
        } else {
            $folder = 'Sent';
        }

        $message['PrivateMessage']['folder'] = $folder;
        $this->PrivateMessage->save($message);
        $this->redirect(array('action' => 'folder', $folder));
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
        Sanitize:: paranoid($messageId);
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
     * @param string $toUserLogin      The login, or the string containing
     * various login separated by a comma, to which we have to send the message
     * @param int    $replyToMessageId The identifier of the message to
     * which the present message is an answer
     *
     * @return void
     */
    public function write($toUserLogin = '', $replyToMessageId = null)
    {

        Sanitize::html($toUserLogin);
        Sanitize::paranoid($replyToMessageId);
        if ($replyToMessageId != null) {
            $message = $this->PrivateMessage->findById($replyToMessageId);
            $this->set('isAReply', true);
            $this->set('replyToTitle', $message['PrivateMessage']['title']);
            $this->set(
                'replyToContent',
                $this->PrivateMessage->formatReplyMessage(
                    $message['PrivateMessage']['content'],
                    $toUserLogin
                )
            );
        } else {
            $this->set('isAReply', false);
            $this->set('replyToTitle', '');
        }

        if ($toUserLogin != '') {
            $this->set('toUserLogin', $toUserLogin);
        } else {
            $this->set('toUserLogin', '');
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
        Sanitize::paranoid($type);
        Sanitize::paranoid($joinObjectId);

        if ($type != null && $joinObjectId != null) {
            $this->params['action'] = 'write';
            $this->set('msgPreContent', '['.$type.':'.$joinObjectId.']');
            $this->write();
        } else {
            $this->redirect(array('action' => 'write'));
        }
    }

    /**
     * No view behind this function, which aim is to inform the user
     * how many unread messages stay on his inbox.
     * This function is called in top1.ctp
     *
     * @return Array
     */
    public function check()
    {
        return $this->PrivateMessage->find(
            'count',
            array(
                'conditions' => array(
                    'PrivateMessage.recpt' => $this->Auth->user('id'),
                    'PrivateMessage.folder' => 'Inbox',
                    'PrivateMessage.isnonread' => 1)
            )
        );
    }

}
?>
