<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
controller for the wall
should contains all the method related to send a new message on the wall
replying and so
+---------+--------------+------+-----+---------+----------------+
| Field   | Type         | Null | Key | Default | Extra          |
+---------+--------------+------+-----+---------+----------------+
| id      | int(11)      | NO   | PRI | NULL    | auto_increment | 
| owner   | int(11)      | NO   |     | NULL    |                | 
| replyTo | int(11)      | YES  |     | NULL    |                | 
| date    | datetime     | NO   |     | NULL    |                | 
| title   | varchar(255) | NO   |     | NULL    |                | 
| content | text         | NO   |     | NULL    |                | 
+---------+--------------+------+-----+---------+----------------+

 
*/

// Permit to sanitize input, to avoid xss
App::import('Core', 'Sanitize');

class WallController extends Appcontroller{
    

    var $name = 'Wall' ;
    var $paginate = array('limit' => 50);
    var $helpers = array('Wall','Javascript');

    function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allowedActions = array('*');
           
        // TODO complete this method
    }

    function index(){
        $firstMessages = $this->Wall->find('all',
            array(
                "order" => "Wall.date", 
                "conditions" => array ("Wall.replyTo" => 0),
                "contain"    => array (
                    "Reply" => array (
                        "order" =>"Reply.date",
                        "fields" => array("Reply.id") 
                        )
                    
                    ,"User" => array (
                        "fields" => array("User.image","User.username") 
                        )
                    ) 
                )
            );

        
        $messages = $this->Wall->find('all',
            array(
                "order" => "Wall.id", 
                "contain"    => array (
                    "Reply" => array (
                        "order" =>"Reply.date",
                        "fields" => array("Reply.id") 
                        )
                    
                    ,"User" => array (
                        "fields" => array("User.image","User.username") 
                        )
                    ) 
                )
            );
        
        $this->set('allMessages' , $messages) ;
        $this->set('firstMessages' , $firstMessages) ;

    }

    function save(){
        //TODO
        if(!empty($this->data['Wall']['content'] )){
            Sanitize::stripScripts( $this->data['Wall']['content']);
            $this->data['Wall']['owner'] = $this->Auth->user('id');
            $this->data['Wall']['date'] = date("Y-m-d H:i:s");  
            // now save to database 
            if ($this->Wall->save($this->data)){

            }
        }
        $this->redirect(array('action'=>'index'));
    }

   
   
    function save_inside(){
        $idTemp = $this->Auth->user('id');
        if( isset($_POST['content'])
            AND  rtrim($_POST['content']) != ''
            AND isset($_POST['replyTo'])
            AND !(empty($idTemp))
            ) {
             
            Sanitize::stripScripts( $_POST['content']);
            $this->data['Wall']['content'] = $_POST['content'] ; 
            $this->data['Wall']['owner'] = $idTemp ;
            $this->data['Wall']['replyTo'] = $_POST['replyTo'] ;
            $this->data['Wall']['date'] = date("Y-m-d H:i:s"); 
            // now save to database 
            if ($this->Wall->save($this->data)){
                
                $user = new User();
                $user->id = $idTemp ;
                $user->recursive = -1 ;
                $user = $user->read();
                $this->set("user" , $user ); 
                
                // we forge a message to be used in the view
                // TODO find a way to retrieve the id of the just-saved message
                // in case a guy try to reply to itself without reloading the page
                
                $message['Wall']['content'] = $_POST['content'] ; 
                $message['Wall']['owner'] = $idTemp ;
                $message['Wall']['replyTo'] = $_POST['replyTo'] ;
                $message['Wall']['date'] = date("Y-m-d H:i:s");
                $message['Wall']['id'] = '' ; // TODO find how to to retrive this value
                 
                $message['User']['image'] = $user['User']['image'];
                $message['User']['username'] = $user['User']['username'];

                $this->set("message" , $message ); 
            }
        }
    }



}


?>
