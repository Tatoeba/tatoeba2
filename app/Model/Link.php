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

    public $belongsTo = array(
        'Sentence'
    );

    public function beforeSave() {
        if (   isset($this->data[$this->alias]['sentence_id'])
            && isset($this->data[$this->alias]['translation_id'])) {
            $duplicate = $this->find('first', array('conditions' => array(
                'sentence_id' => $this->data[$this->alias]['sentence_id'],
                'translation_id' => $this->data[$this->alias]['translation_id']
            )));
            if ($duplicate)
                return false;
        }
        return true;
    }

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
        $this->flagSentencesToReindex(
            $this->data['Link']['sentence_id'],
            $this->data['Link']['translation_id']
        );
    }

    private function flagSentencesToReindex($sentence, $translation)
    {
        $impactedSentences = array_merge(
            $this->findDirectTranslationsIds($sentence),
            $this->findDirectTranslationsIds($translation)
        );
        $impactedSentences = array_keys(array_flip($impactedSentences));
        $this->Sentence->needsReindex($impactedSentences);
    }

    /**
     * Called before a link is deleted.
     *
     * @return void
     */
    public function beforeDelete($cascade = true)
    {
        $aboutToDelete = $this->findById($this->id);
        $Contribution = ClassRegistry::init('Contribution');
        if (isset($aboutToDelete['Link'])) {
            $Contribution->saveLinkContribution(
                $aboutToDelete['Link']['sentence_id'],
                $aboutToDelete['Link']['translation_id'],
                'delete'
            );
        }
        $this->flagSentencesToReindex(
            $aboutToDelete['Link']['sentence_id'],
            $aboutToDelete['Link']['translation_id']
        );
        return true;
    }

    /**
     * Add link.
     * NOTE: This will add 2 entries. One for A->B and one for B->A.
     *
     * @param int    $sentenceId      Id of the sentence.
     * @param int    $translationId   Id of the translation.
     * @param string $sentenceLang    Language of the sentence.
     * @param string $translationLang Language of the translation.
     *
     * @return bool
     */
    public function add(
        $sentenceId, 
        $translationId, 
        $sentenceLang = null, 
        $translationLang = null
    ) {
        $sentenceId = intval($sentenceId);
        $translationId = intval($translationId);

        // Check if we're linking the sentence to itself.
        if ($sentenceId == $translationId) {
            return false;
        }
        
        
        if ($sentenceLang != null && $translationLang != null) {
            // Check whether the sentences exist.
            $result = $this->query("
                SELECT COUNT(*) as count FROM sentences 
                WHERE id IN ($sentenceId, $translationId)
            ");
                    
            if ($result[0][0]['count'] < 2) {
                return false;
            }
        } else {
            // Check whether the sentences exist and retrieve
            // the language code for each sentence
            $result = $this->query("
                SELECT Sentence.id, Sentence.lang FROM sentences as Sentence
                WHERE id IN ($sentenceId, $translationId)
            ");

            if (count($result) != 2) {
                return false;
            }

            foreach ($result as $sentence) {
                if ($sentence['Sentence']['id'] == $sentenceId) {
                    $sentenceLang = $sentence['Sentence']['lang'];
                }
                if ($sentence['Sentence']['id'] == $translationId) {
                    $translationLang = $sentence['Sentence']['lang'];
                }
            }
        }
        
        
        // Saving links if sentences exist.
        $data[0]['sentence_id'] = $sentenceId;
        $data[0]['translation_id'] = $translationId;
        $data[0]['sentence_lang'] = $sentenceLang;
        $data[0]['translation_lang'] = $translationLang;
        $data[1]['sentence_id'] = $translationId;
        $data[1]['translation_id'] = $sentenceId;
        $data[1]['sentence_lang'] = $translationLang;
        $data[1]['translation_lang'] = $sentenceLang;
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
    public function deletePair($sentenceId, $translationId)
    {
        $toRemove = $this->find('all', array(
            'conditions' => array(
                'or' => array(
                    array('and' => array(
                        'Link.sentence_id'    => $translationId,
                        'Link.translation_id' => $sentenceId,
                    )),
                    array('and' => array(
                        'Link.sentence_id'    => $sentenceId,
                        'Link.translation_id' => $translationId,
                    ))
                )
            ),
            'fields' => array('id'),
            'limit' => 2,
        ));
        $toRemove = Set::extract($toRemove, '{n}.Link.id');
        if ($toRemove) {
            $deleted = $this->deleteAll(array('Link.id' => $toRemove), false, true);
        }

        return $deleted;
    }

    public function findDirectTranslationsIds($sentenceId) {
        $links = $this->find('all', array(
            'conditions' => array('sentence_id' => $sentenceId),
            'fields' => array('translation_id'),
        ));
        return Set::classicExtract($links, '{n}.Link.translation_id');
    }

    public function findDirectAndIndirectTranslationsIds($sentenceId) {
        $links = $this->find('all', array(
            'joins' => array(
                array(
                    'table' => $this->useTable,
                    'alias' => 'Translation',
                    'type' => 'inner',
                    'conditions' => array(
                        'Link.translation_id = Translation.sentence_id'
                    )
                )
            ),
            'conditions' => array(
                'Link.sentence_id' => $sentenceId,
            ),
            'fields' => array(
                'DISTINCT Link.sentence_id',
                'IF(Link.sentence_id = Translation.translation_id, Translation.sentence_id, Translation.translation_id) as translation_id'
            )
        ));
        return Set::classicExtract($links, '{n}.0.translation_id');
    }

    public function updateLanguage($sentenceId, $lang)
    {
        $this->updateAll(
            array('sentence_lang' => "'$lang'"),
            array('sentence_id' => $sentenceId)
        );

        $this->updateAll(
            array('translation_lang' => "'$lang'"),
            array('translation_id' => $sentenceId)
        );
    }
}
?>
