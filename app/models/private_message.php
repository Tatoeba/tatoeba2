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
 * Model for Private Messages.
 *
 * @category PrivateMessage
 * @package  Models
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class PrivateMessage extends AppModel
{
    public $name = 'PrivateMessage';

    public $actsAs = array('Containable');
    public $belongsTo = array(
        'User',
        'Recipient' => array(
            'className' => 'User',
            'foreignKey' => 'recpt'
        ),
        'Sender' => array(
            'className' => 'User',
            'foreignKey' => 'sender'
        )
    );

    /**
     * function to retrieve private message by folder
     *
     * @param string $folderId Name of the folder we want the messages
     * @param int    $userId   Id of the user we want the messages
     *
     * @return Array
     */
    public function getMessages($folderId, $userId)
    {
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'PrivateMessage.user_id' => $userId,
                    'PrivateMessage.folder' => $folderId
                ),
                'limit'=> 10,
                'order' => 'PrivateMessage.date DESC',
                'contain' => array(
                    'Sender' => array(
                        'fields' => array('username')
                    ),
                    'Recipient' => array(
                        'fields' => array('username')
                    )
                )
            )
        );
    }
    
    /**
     * Return message corresponding to given id.
     *
     * @param int $messageId Id of the message to retrieve.
     *
     * @return Array
     */
    public function getMessageWithId($messageId)
    {
        return $this->find(
            'first',
            array(
                'conditions' => array('PrivateMessage.id' => $messageId),
                'contain' => array(
                    'Sender' => array(
                        'fields' => array('username')
                    )
                )
            )
        );
    }

    /**
     * function to format the text of the messages in case of answer
     *
     * @param string $content The content of the message
     * @param string $login   The author of the original message
     *
     * @return string
     */
    public function formatReplyMessage($content, $login)
    {
        $messNextRegExp = preg_replace("#\r?\n#iU", " ", $content);
        $messNextRegExp = preg_replace(
            "#\r?\n#iU", "\n > ",
            wordwrap($messNextRegExp, 50)
        );
        return "\n" . sprintf(__('%s wrote:', true), $login) . "\n > "
            . $messNextRegExp;
    }
}
?>
