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
 * Model for links. Links indicate which sentence is translation of which.
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/
class Link extends AppModel
{
    public $useTable = 'sentences_translations';
    
    
    /**
     * Called after a link is saved.
     * 
     * @param bool $created true if a new line has been created.
     *                      false if a line has been updated.
     * 
     * @return void
     */
    public function afterSave($created)
    {
        ClassRegistry::init('Contribution')->saveLinkContribution(
            $this->data['Link']['sentence_id'],
            $this->data['Link']['translation_id'],
            'insert'
        );
    }
    
    
    /**
     * Called after a link is deleted.
     * 
     * @return void
     */
    public function afterDelete()
    {
        $Contribution = ClassRegistry::init('Contribution');

	$Contribution->saveLinkContribution(
            $this->data['Link']['sentence_id'],
            $this->data['Link']['translation_id'],
            'delete'
        );
        
        // We need to add manually the reciprochal link deletion because the
        // callback is called manually.
        $Contribution->saveLinkContribution(
            $this->data['Link']['translation_id'],
            $this->data['Link']['sentence_id'],
            'delete'
        );
    }
    
    /**
     * Add link.
     * NOTE: This will add 2 entries. One for A->B and one for B->A.
     *
     * @param int $sentenceId    Id of the sentence.
     * @param int $translationId Id of the translation.
     *
     * @return bool
     */
    public function add($sentenceId, $translationId)
    {
        $sentenceId = intval($sentenceId);
        $translationId = intval($translationId);
        
        // Check if we're linking the sentence to itself.
        if ($sentenceId == $translationId) {
            return false;
        }
        
        // Check if the sentences exist.
        $result = $this->query("
            SELECT COUNT(*) as count FROM sentences 
            WHERE id IN ($sentenceId, $translationId)
        ");
        
        if ($result[0][0]['count'] < 2) {
            return false;
        }
        
        // Saving links if sentences exist.
        $data[0]['sentence_id'] = $sentenceId;
        $data[0]['translation_id'] = $translationId;
        $data[1]['sentence_id'] = $translationId;
        $data[1]['translation_id'] = $sentenceId;
        return $this->saveAll($data);
    }
    
    /**
     * Delete link.
     * NOTE: This will remove 2 entries. One for A->B and one for B->A.
     *
     * @param int $sentenceId    Id of the sentence.
     * @param int $translationId Id of the translation.
     *
     * @return bool
     */
    public function delete($sentenceId, $translationId)
    {
        // custom query to avoid having to create an 'id' field.
        $this->query("
            DELETE FROM sentences_translations 
            WHERE (sentence_id = $sentenceId AND translation_id = $translationId)
               OR (sentence_id = $translationId AND translation_id = $sentenceId) 
        ");
        
        $this->data['Link']['sentence_id'] = $sentenceId;
        $this->data['Link']['translation_id'] = $translationId;
        $this->afterDelete(); // calling callback manually...
                
        return true; // yes, it's useless, never mind...
    }
}
?>
