<?php
/**
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)
    Copyright (C) 2009  Allan SIMON (allan.simon@supinfo.com)

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
 *
 * PHP version 5 
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model Class which represent sentences
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/
class Link extends AppModel
{
    public $useTable = 'sentences_translations';
    
    
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
        $conditions = array(
            'OR' => array(
                array(
                    'sentence_id' => $sentenceId,
                    'translation_id' => $translationId
                ),
                array(
                    'sentence_id' => $translationId,
                    'translation_id' => $sentenceId
                )
            )
        );
        
        return $this->deleteAll($conditions);
    }
}
?>