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

    public $helpers = array(
        'Comments',
        'Html', 
        'Date',
        'PrivateMessages',
        'Wall' // TODO Move the displayMessagePosterImage() method of
               // WallHelper into a more general helper
    );
    

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
     *
     * @return void
     */
    public function folder($folder = 'Inbox')
    {
        $folder = Sanitize::paranoid($folder);
        
        $content = $this->PrivateMessage->getMessages(
            $folder,
            $this->Auth->user('id')
        );
        
        $this->set('folder', $folder);
        $this->set('content', $content);
    }

    /**
     * This function has to send the message, then to display the sent folder
     *
     * @return void
     */
    public function send()
    {
        if (!empty($this->data['PrivateMessage']['recpt'])
            && !empty($this->data['PrivateMessage']['content'])
        ) {
            $this->data['PrivateMessage']['sender'] = $this->Auth->user('id');
            
            $recptArray = explode(',', $this->data['PrivateMessage']['recpt']);
            
            // loop to send msg to different dest.
            foreach ($recptArray as $recpt) {
            
                $recpt = trim($recpt);
                $recptId = $this->PrivateMessage->User->getIdFromUsername($recpt);

                // we send the msg only if the user exists.
                if ($recptId) {

                    $this->data['PrivateMessage']['recpt'] = $recptId;
                    $this->data['PrivateMessage']['user_id'] = $recptId;
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
                                'The user %s you to want to send this message '.
                                'to does not exist. Please try with another '.
                                'username.',
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
                    'You must fill at least the "To" field and the content field.',
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

        $messageId = Sanitize::paranoid($messageId);
        /**
         * The following lines of code check if a message is read, or not
         * and change is read value automatically.
         */
        $message = $this->PrivateMessage->getMessageWithId($messageId);
        if ($message['PrivateMessage']['isnonread'] == 1) {
            $message['PrivateMessage']['isnonread'] = 0;
            $this->PrivateMessage->save($message);
        }

        $this->set('message', $message);

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
        $messageId = Sanitize::paranoid($messageId);
        
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

        $messageId = Sanitize::paranoid($messageId);
        
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
     *
     * @return void
     */
    public function write($recipients = null)
    {
        if ($recipients == null) {
            $recipients = '';
        }
        
        $this->set('recipients', $recipients);        
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
