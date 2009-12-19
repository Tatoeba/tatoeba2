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

class Wall extends AppModel{
    var $name = 'Wall';
    var $useTable = 'wall';
    var $actsAs = array('Containable');

	var $belongsTo = array('User' => array('className' => 'User', 'foreignKey'
    => 'owner'));


    var $hasMany = array( 'Reply' =>
         array(
            'className' => 'Wall',
            'foreignKey' => 'replyTo',
            'order' => 'date DESC',
            'dependent'=> false
        )
    );
     
    function afterSave($created){
        if(isset($this->data['Wall']['content'])){
            $data['newMessage']['owner'] = $this->data['Wall']['owner'] ;
            $data['newMessage']['date'] = date("Y-m-d H:i:s");
        }

    }

    /*
    ** Get all the message which start a thread
    */
    function getFirstMessages(){
       return  $this->find('all',
            array(
                "order" => "Wall.date DESC", 
                "conditions" => array ("Wall.replyTo" => 0),
                "contain"    => array (
                    "Reply" => array (
                        "order" =>"Reply.date",
                        "fields" => array("Reply.id") 
                        )
                    
                    ,"User" => array (
                        "fields" => array("User.image","User.username","User.id") 
                        )
                    ) 
                )
            );
    }

    /*
    ** get all Messages
    */

    function getMessages(){
        return $this->find('all',
            array(
                "order" => "Wall.id", 
                "contain"    => array (
                    "Reply" => array (
                        "order" =>"Reply.date ",
                        "fields" => array("Reply.id") 
                        )
                    
                    ,"User" => array (
                        "fields" => array("User.image","User.username", "User.id") 
                        )
                    ) 
                )
            );
    }

    /*
    ** get the 10 last messages posted
    */

    function getLastMessages($numberOfLastMessages){
        return $this->find('all',
            array(
                "order" => "Wall.date DESC",
                "limit" => $numberOfLastMessages,  
                "contain"    => array (
                    "User" => array (
                        "fields" => array("User.username", "User.id") 
                        )
                    ) 
                )
            );
    }
    


}
?>
