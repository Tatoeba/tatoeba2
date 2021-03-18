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

use Cake\Datasource\QueryInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class LinksTable extends Table
{
    public function initialize(array $config)
    {
        $this->setTable('sentences_translations');
        
        $this->belongsTo('Sentences')
             ->setJoinType(QueryInterface::JOIN_TYPE_INNER);
        $this->belongsTo('Translations')
             ->setJoinType(QueryInterface::JOIN_TYPE_INNER);
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

    private function logLinksAndFlagSentences($sentenceId, $translationId)
    {
        $Contribution = TableRegistry::getTableLocator()->get('Contributions');
        $Contribution->saveLinkContribution(
            $sentenceId,
            $translationId,
            'delete'
        );
        $this->flagSentencesToReindex(
            $sentenceId,
            $translationId
        );
    }

    public function afterDelete($event, $entity, $options) {
        $this->logLinksAndFlagSentences(
            $entity->sentence_id,
            $entity->translation_id
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

        $ids = [$sentenceId, $translationId];
        if ($sentenceLang != null && $translationLang != null) {
            // Check whether the sentences exist.
            $result = $this->Sentences->find('all')
                ->where(['id' => $ids], ['id' => 'integer[]'])
                ->count();

            if ($result < 2) {
                return false;
            }
        } else {
            // Check whether the sentences exist and retrieve
            // the language code for each sentence
            $result = $this->Sentences->find('all')
                ->where(['id' => $ids], ['id' => 'integer[]'])
                ->select(['id', 'lang'])
                ->toList();

            if (count($result) != 2) {
                return false;
            }

            foreach ($result as $sentence) {
                if ($sentence->id == $sentenceId) {
                    $sentenceLang = $sentence->lang;
                }
                if ($sentence->id == $translationId) {
                    $translationLang = $sentence->lang;
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
        $conditions = ['OR' => [
            [
                'sentence_id'    => $translationId,
                'translation_id' => $sentenceId,
            ],
            [
                'sentence_id'    => $sentenceId,
                'translation_id' => $translationId,
            ]
        ]];

        $deleted = $this->deleteAll($conditions);

        if ($deleted == 2) {
            // deleteAll() doesn't trigger afterSave()
            $this->logLinksAndFlagSentences($sentenceId, $translationId);
            $this->logLinksAndFlagSentences($translationId, $sentenceId);
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
        $links = $this->find('all')
            ->join([
                [
                    'table' => $this->getTable(),
                    'alias' => 'Translation',
                    'type' => 'inner',
                    'conditions' => ['Links.translation_id = Translation.sentence_id']
                ]
            ])
            ->where(['Links.sentence_id' => $sentenceId])
            ->select([
                'sentence_id' => 'DISTINCT(Links.sentence_id)',
                'translation_id' => 'IF(Links.sentence_id = Translation.translation_id, Translation.sentence_id, Translation.translation_id)'
            ])
            ->toList();
        $links = Hash::extract($links, '{n}.translation_id');
        return is_null($links) ? array() : $links;
    }

    public function updateLanguage($sentenceId, $lang)
    {
        $this->updateAll(
            ['sentence_lang' => $lang],
            ['sentence_id' => $sentenceId]
        );

        $this->updateAll(
            ['translation_lang' => $lang],
            ['translation_id' => $sentenceId]
        );
    }
}
