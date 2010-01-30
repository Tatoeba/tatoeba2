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


// Permit to sanitize input, to avoid xss
App::import('Core', 'Sanitize');
/**
 * Controller for the wall.
 *
 * @category Contributions
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class WallController extends Appcontroller
{
    

    public $name = 'Wall' ;
    public $paginate = array('limit' => 50);
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
        $this->Auth->allowedActions = array('*');
           
        // TODO complete this method
    }

    /**
     * display main wall page with all messages
     * TODO need to paginate it
     *
     * @return void
     */

    public function index()
    {
        $firstMessages = $this->Wall->getFirstMessages();

        
        $messages = $this->Wall->getMessages();
        $tenLastMessages = $this->Wall->getLastMessages(10);
        
        $this->set('allMessages', $messages);
        $this->set('tenLastMessages', $tenLastMessages);
        $this->set('firstMessages', $firstMessages);

    }

    /**
     * save a new first message
     *
     * @return void
     */

    public function save()
    {
        //TODO
        if (!empty($this->data['Wall']['content'])) {
            Sanitize::html($this->data['Wall']['content']);
            $this->data['Wall']['owner'] = $this->Auth->user('id');
            $this->data['Wall']['date'] = date("Y-m-d H:i:s");  
            // now save to database 
            if ($this->Wall->save($this->data)) {
            }
        }
        $this->redirect(array('action'=>'index'));
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
             
            Sanitize::stripScripts($_POST['content']);
            $this->data['Wall']['content'] = $_POST['content'] ; 
            $this->data['Wall']['owner'] = $idTemp ;
            $this->data['Wall']['replyTo'] = $_POST['replyTo'] ;
            $this->data['Wall']['date'] = date("Y-m-d H:i:s"); 
            // now save to database 
            if ($this->Wall->save($this->data)) {
                
                App::import('Model', 'User');
                $userModel =  new User(); 
                $user = $userModel->getInfoWallUser($idTemp);
                $this->set("user", $user); 
                
                // we forge a message to be used in the view
                
                $message['Wall']['content'] = $_POST['content'] ; 
                $message['Wall']['owner'] = $idTemp ;
                $message['Wall']['replyTo'] = $_POST['replyTo'] ;
                $message['Wall']['date'] = date("Y-m-d H:i:s");
                $message['Wall']['id'] = $this->Wall->id ;
                 
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
                $parentMessage = $this->Wall->getMessageForMail($_POST['replyTo']);
                
                // prepare email
                // TODO : i18n mail

                if ($parentMessage['User']['send_notifications']
                    && $parentMessage['User']['id'] != $this->Auth->user('id')
                ) {
                    $participant = $parentMessage['User']['email'];
                    $subject  = 'Tatoeba - ' .
                         $message['User']['username'] .
                         ' has replied to you on the Wall';
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



}


?>
