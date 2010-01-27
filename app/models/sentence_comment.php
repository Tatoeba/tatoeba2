<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for sentence comments.
 *
 * @category SentenceComments
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceComment extends AppModel
{
    public $actsAs = array('Containable');
    public $belongsTo = array('Sentence', 'User');

    /**
     * Get number of sentences owned by a user.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function numberOfCommentsOwnedBy($userId)
    {
        return $this->find(
            'count',
            array(
                'conditions' => array( 'SentenceComment.user_id' => $userId)
             )
        );

    }
    
    /**
     * Return latest comments for each language.
     *
     * @return array
     */
    public function getLatestCommentsInEachLanguage()
    {
        $langs = array('eng', 'fra', 'jpn', 'spa', 'deu');
        $sentenceComments = array();
        
        foreach ($langs as $lang) {
            $sentenceComments[$lang] = $this->find(
                "all",
                array( 
                    "conditions" => array("SentenceComment.lang" => $lang),
                    "limit"=> 10,
                    "order" => "SentenceComment.created DESC"
                )
            );
        }
        
        $sentenceComments['unknown'] = $this->find(
            "all",
            array( 
                "conditions" => array(
                    "NOT" => array("SentenceComment.lang" => $langs)
                ),
                "limit"=> 10,
                "order" => "SentenceComment.created DESC"
            )
        );
        
        return $sentenceComments;
    }
    
    /**
     * Return comments for given sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getCommentsForSentence($sentenceId)
    {
        return $this->find(
            'all', 
            array(
                'conditions' => array('SentenceComment.sentence_id' => $sentenceId),
                'order' => 'SentenceComment.created'
            )
        );
    }
    
    /**
     * Return latest comments.
     *
     * @param int $limit Number of comments to be retrieved.
     *
     * @return array
     */
    public function getLatestComments($limit)
    {
        return $this->find(
            'all',
            array('order' => 'SentenceComment.created DESC', 'limit' => $limit)
        );
    }
    
    /**
     * Return emails of users who posted a comment on the sentence
     * and who didn't disable notification.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getEmailsFromComments($sentenceId)
    {
        $emails = array();
        $comments = $this->find(
            'all',
            array(
                'conditions' => array('SentenceComment.sentence_id' => $sentenceId),
                'contain' => array ('User')
            )
        );
        foreach ($comments as $comment) {
            if ($comment['User']['send_notifications']) {
                $emails[] = $comment['User']['email'];
            }
        }
        $emails = array_unique($emails);
        return $emails;
    }
    
    /**
     * Return email of owner of the sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getEmailFromSentence($sentenceId)
    {
        $sentence = $this->Sentence->find(
            'first',
            array(
                'conditions' => array('Sentence.id' => $sentenceId),
                'contain' => array ('User')
            )
        );
        if (isset($sentence) AND $sentence['User']['send_notifications']) {
            return $sentence['User']['email'];
        } else {
            return null;
        }
    }
}
?>