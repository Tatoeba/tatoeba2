<?php
/**
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
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * PHP version 5 
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class which represent the wall and messages posted on
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/

class Wall extends AppModel
{
    public $name = 'Wall';
    public $useTable = 'wall';
    public $actsAs = array('Containable');

    public $belongsTo = array(
        'User' => array(
            'className'  => 'User',
            'foreignKey' => 'owner'
        )
    );

    public $hasMany = array(
        'Reply' =>
             array(
                'className' => 'Wall',
                'foreignKey' => 'replyTo',
                'order' => 'date DESC',
                'dependent'=> false
            )
    );
    
    /**
     * used after a save is made in the database
     *
     * @param bool $created if succeed or not TODO: to check
     * 
     * @return void
     */ 
     
    public function afterSave($created)
    {
        if (isset($this->data['Wall']['content'])) {
            $data['newMessage']['owner'] = $this->data['Wall']['owner'] ;
            $data['newMessage']['date'] = date("Y-m-d H:i:s");
        }

    }

    /**
     * Get all the message which start a thread
     *
     * @return array of all messages which start a thread
     */

    public function getFirstMessages()
    {
        return  $this->find(
            'all',
            array(
                "order" => "Wall.date DESC", 
                "conditions" => array ("Wall.replyTo" => 0),
                "contain"    => array (
                    "Reply" => array (
                        "order" =>"Reply.date",
                        "fields" => array("Reply.id") 
                        )
                    
                    ,"User" => array (
                        "fields" => array(
                            "User.image",
                            "User.username",
                            "User.id"
                        ) 
                    )
                ) 
            )
        );
    }

    /**
     * get all Messages
     *
     * @return array of all messages
     */

    public function getMessages()
    {
        return $this->find(
            'all',
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

    /**
     * get the X last messages posted
     *
     * @param int $numberOfLastMessages number of messages wanted
     *
     * @return array of these messages
     */

    public function getLastMessages($numberOfLastMessages)
    {
        return $this->find(
            'all',
            array(
                "order" => "Wall.date DESC",
                "limit" => $numberOfLastMessages,  
                "contain"    => array (
                    "User" => array (
                        "fields" => array(
                            "User.username",
                            "User.id"
                        ) 
                    )
                ) 
            )
        );
    }

    /**
     * retrieve information of a parent message
     * needed to generate an email
     * 
     * @param int $parentMessageId id of the parent message
     *
     * @return array
     */

    public function getMessageForMail($parentMessageId)
    {
        return $this->find(
            'first',
            array(
                "order" => "Wall.id",
                "fields"=> array('Wall.id'),
                "conditions" => array("Wall.id" => $parentMessageId),
                "contain"    => array(
                    "User" => array (
                        "fields" => array(
                            "User.username",
                            "User.id",
                            "User.email",
                            "User.send_notifications",
                        ) 
                    )
                ) 
            )
        );
    }

}
?>
