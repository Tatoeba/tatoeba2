<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>
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
 * @link     http://tatoeba.org
 */

/**
 * Controller for the wall.
 *
 * @category Wall
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class WallController extends Appcontroller
{
    

    public $name = 'Wall' ;
    public $paginate = array(
        "order" => "WallThread.last_message_date DESC", 
        "limit" => 10,
        "fields" => array ("lft",'rght'), 
        "conditions" => array (
            "Wall.parent_id" => null ,
        ),
        "contain" => array (
            "WallThread" => array(
                'fields' => "last_message_date"
            )
        )
    );
    public $helpers = array('Wall','Javascript','Date');
    public $components = array ('Mailer');
    /**
     * to know who can do what
     *
     * @return void
     */

    public function beforeFilter()
    {
        parent::beforeFilter();
        // TODO set correct right
        $this->Auth->allowedActions = array(
            'index',
            'delete_message',
            'show_message',
            'generate_new_wall'
        );
    }

    /**
     * display main wall page with all messages
     * TODO need to paginate it
     *
     * @return void
     */
    public function index()
    {
        
        //$messages = $this->_organize_messages($messages);
        $tenLastMessages = $this->Wall->getLastMessages(10);
        //pr($messages);
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');

        $messagesIdDirty = $this->paginate();
        $messageLftRght = array();
        foreach ($messagesIdDirty as $messageDirty) {
            array_push($messageLftRght, $messageDirty['Wall']);
        }
        
        $messages = $this->Wall->getMessagesThreaded($messageLftRght);

        
        $messages = $this->Permissions->getWallMessagesOptions(
            $messages,
            $userId,
            $groupId
        );
         
        $isAuthenticated = !empty($userId);

        //pr($messagesPermissions);
        $this->set('isAuthenticated', $isAuthenticated); 
        //$this->set('messagesPermissions', $messagesPermissions); 
        $this->set('allMessages', $messages);
        $this->set('tenLastMessages', $tenLastMessages);
        //$this->set('firstMessages', $firstMessages);
        

    }

    /**
     * use to organize the messages array the following way
     * message_id => message, we need it as deleted message
     * will shift the index
     * 
     * @param array $messages The messages array to organize
     *
     * @return array
     */

    private function _organize_messages($messages)
    {
        $newMessages = array();
        foreach ($messages as $message) {
            $newMessages[$message['Wall']['id']] = $message;
        }

        return $newMessages;
    }
    
    /**
     * save a new first message
     *
     * @return void
     */

    public function save()
    {

        Sanitize::html($this->data['Wall']['content']);
        if (!empty($this->data['Wall']['content'])
            && $this->Auth->user('id')
        ) {
            $now = date("Y-m-d H:i:s");  

            $this->data['Wall']['owner'] = $this->Auth->user('id');
            $this->data['Wall']['date'] = $now; 
            // now save to database 
            if ($this->Wall->save($this->data)) {
                $this->update_thread_date(
                    $this->Wall->id,
                    $now
                );
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
        
        $idTemp = $this->Auth->user('id');
        if (isset($_POST['content'])
            && rtrim($_POST['content']) != ''
            && isset($_POST['replyTo'])
            && !(empty($idTemp))
        ) {

            $content = $_POST['content'];
            $parentId = $_POST['replyTo'];
            $now = date("Y-m-d H:i:s"); 
             
            Sanitize::stripScripts($content);
            $this->data['Wall']['content'] = $content ; 
            $this->data['Wall']['owner'] = $idTemp ;
            $this->data['Wall']['parent_id'] = $parentId ;
            $this->data['Wall']['date'] = $now;
            // now save to database 
            if ($this->Wall->save($this->data)) {
                $newMessageId = $this->Wall->id ;
                
                $User =  ClassRegistry::init('User');
                $user = $User->getInfoWallUser($idTemp);
                $this->set("user", $user); 
               
                $this->update_thread_date($newMessageId, $now);
                
                // we forge a message to be used in the view
                
                $message['Wall']['content'] = $content; 
                $message['Wall']['owner'] = $idTemp;
                $message['Wall']['parent_id'] = $parentId;
                $message['Wall']['date'] = $now;
                $message['Wall']['id'] = $newMessageId; 
                 
                $message['User']['image'] = $user['User']['image'];
                if (empty($message['User']['image'])) {
                    $message['User']['image'] = 'unknown-avatar.jpg';
                }

                $message['User']['username'] = $user['User']['username'];

                $this->set("message", $message); 
                
                // ------------------
                // send notification
                // ------------------
                
                // Retrieve parent message
                $parentMessage = $this->Wall->getMessageForMail($parentId);
                
                // prepare email
                // TODO : i18n mail

                if ($parentMessage['User']['send_notifications']
                    && $parentMessage['User']['id'] != $idTemp 
                ) {
                    $participant = $parentMessage['User']['email'];
                    $subject  = 'Tatoeba - ' .
                         $message['User']['username'] .
                         ' has replied to you on the Wall';

                    //TODO add a mechanism if the link is not on first page
                    $mailContent 
                        = 'http://' .
                        $_SERVER['HTTP_HOST'] .
                        '/wall/index#message_'.$message['Wall']['id']."\n\n";
                    $mailContent .= '- - - - - - - - - - - - - - - - -'."\n\n";
                    $mailContent .= $message['Wall']['content']."\n\n";
                    $mailContent .= '- - - - - - - - - - - - - - - - -'."\n\n";
                    
                    $this->Mailer->to = $participant;
                    $this->Mailer->toName = '';
                    $this->Mailer->subject = $subject;
                    $this->Mailer->message = $mailContent;
                    $this->Mailer->send();
                }
            }
        }
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
        $messageOwnerId = $this->Wall->getOwnerIdOfMessage($messageId);
        //we check a second time even if it has been checked while displaying
        // or not the delete icon, but one can try to directly call delete_message
        // so we need to recheck
        $messagePermissions = $this->Permissions->getWallMessageOptions(
            null,
            $messageOwnerId,
            $this->Auth->user('id'),
            $this->Auth->user('group_id')
        );
        if ($messagePermissions['canDelete']) {
            // second parameter "true" => delete in cascade
            $this->Wall->delete($messageId, true);
        }
        // redirect to previous page
        $this->redirect($this->referer()); 
    }

    /**
     * update the WallThread table
     * 
     * @param int  $messageId Message that have been add/updated
     * @param date $newDate   Date of the event.
     *
     * @return void
     */

    public function update_thread_date($messageId, $newDate)
    {
        $WallThread = ClassRegistry::init('WallThread');

        $rootId = $this->Wall->getRootMessageIdOfReply($messageId);
        
        $newThreadData = array(
            'id' => $rootId,
            'last_message_date' => $newDate
        );
        
        $WallThread->save($newThreadData);
    }

    /**
     *
     *
     */
    public function show_message($messageId)
    {
        Sanitize::paranoid($messageId);
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');

        $thread = $this->Wall->getWholeThreadContaining($messageId);

        /* NOTE : have a link to point the thread within the other thread
           is virtually impossible, as the ordering can change between the page
           generation and the user click on the link
        */


        $thread = $this->Permissions->getWallMessagesOptions(
            $thread,
            $userId,
            $groupId
        );
        $this->set("message", $thread[0]);
    }

}


?>
