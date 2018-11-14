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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LinksTable extends Table
{
    public $useTable = 'sentences_translations';

    public function initialize(array $config)
    {
        $this->belongsTo('Sentences');
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->sentence_id && $entity->translation_id) {
            $duplicate = $this->find()
            ->where([
                'sentence_id' => $entity->sentence_id,
                'translation_id' => $entity->translation_id
            ])
            ->first();
                
            if ($duplicate) {
                return false;
            }
        }
        return true;
    }

    public function afterSave($event, $entity, $options = array())
    {
        $Contributions = TableRegistry::getTableLocator()->get('Contributions');
        $Contributions->saveLinkContribution(
            $entity->sentence_id,
            $entity->translation_id,
            'insert'
        );
        $this->flagSentencesToReindex(
            $entity->sentence_id,
            $entity->translation_id
        );
    }

    private function flagSentencesToReindex($sentence, $translation)
    {
        // When (un)linking from $sentence to $translation (one direction),
        // it means $translation can be seen (or stops to be seen)
        // from $sentence and its direct translations, so update them
        // (and them only).
        $impactedSentences = $this->findDirectTranslationsIds($sentence);
        $impactedSentences = array_flip($impactedSentences);
        unset($impactedSentences[$translation]);
        $impactedSentences[$sentence] = null;
        $impactedSentences = array_keys($impactedSentences);
        $this->Sentences->needsReindex($impactedSentences);
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
            $ids = [$sentenceId, $translationId];
            $result = $this->Sentences->find('all')
                ->where(['id' => $ids], ['id' => 'integer[]'])
                ->count();

            if ($result < 2) {
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
        $links = $this->newEntities($data);
        return $this->saveMany($links);
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
        $links = $this->find('all')
            ->where(['sentence_id' => $sentenceId])
            ->select(['translation_id'])
            ->toList();
        $ids = Hash::extract($links, '{n}.translation_id');
        return !empty($ids) ? $ids : array();
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
        $links = Set::classicExtract($links, '{n}.0.translation_id');
        return is_null($links) ? array() : $links;
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
