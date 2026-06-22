<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Validation\Validator;

class UsersSentencesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('users_sentences');

        $this->belongsTo('Sentences');
        $this->belongsTo('Users');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->add('correctness', [
                'inList' => [
                    'rule' => ['inList', [-1, 0, 1]]
                ]
            ]);

        return $validator;
    }

    /**
     * Add sentence to users review
     *
     * @param int $sentenceId
     * @param int $correctness
     * @param int $userId
     *
     * @return array
     */
    public function saveSentence($sentenceId, $correctness, $userId)
    {
        $userSentence = $this->findBySentenceIdAndUserId(
            $sentenceId, $userId
        )->first();

        if (!$userSentence) {
            $userSentence = $this->newEntity([
                'user_id' => $userId,
                'sentence_id' => $sentenceId,
                'correctness' => $correctness
            ]);
        } else {
            $this->patchEntity($userSentence, [
                'correctness' => $correctness,
                'dirty' => 0,
            ]);
        }

        return $this->save($userSentence);
    }

    /**
     * Delete sentence from users review
     *
     * @param int $sentenceId
     * @param int $userId
     *
     * @return boolean
     */
    public function deleteSentence($sentenceId, $userId)
    {
        $userSentence = $this->findBySentenceIdAndUserId(
            $sentenceId, $userId
        )->first();

        if ($userSentence) {
            return $this->delete($userSentence, false);
        }

        return false;
    }

    /**
     * Get correctness for sentence as set by user.
     *
     * @param  int $sentenceId Sentence ID.
     * @param  int $userId     User ID.
     *
     * @return int
     */
    public function correctnessForSentence($sentenceId, $userId)
    {
        $result = $this->find()
            ->where([
                'sentence_id' => $sentenceId,
                'user_id' => $userId
            ])
            ->first();

        if (empty($result)) {
            return -2;
        } else {
            return $result->correctness;
        }
    }

    /**
     * Finder for paginated reviews
     *
     * @param  int    $options['userId']           User ID.
     * @param  int    $options['correctnessLabel'] Label for correctness value.
     * @param  string $options['lang']             Language.
     */
    public function findPaginated(Query $query, array $options)
    {
        $query
            ->select(['id', 'sentence_id', 'correctness', 'modified'])
            ->where(['UsersSentences.user_id' => $options['userId']])
            ->contain([
                'Sentences' => [
                    'fields' => ['id', 'lang', 'text', 'correctness']
                ]
            ]);

        $correctness = $this->correctnessValueFromLabel(
            $options['correctnessLabel']
        );

        if (is_int($correctness)) {
            $query->where(['UsersSentences.correctness' => $correctness]);
        } elseif ($correctness === 'outdated') {
            $query->where(['UsersSentences.dirty' => true]);
        }

        if (!empty($lang)) {
            $query->where(['lang' => $lang]);
        }

        return $query;
    }

    /**
     * Get correctness integer from label, if it exists.
     *
     * @param  string $label Correctness label used in route.
     *
     * @return int|string
     */
    private function correctnessValueFromLabel($label)
    {
        $values = [
            'not-ok' => -1,
            'unsure' => 0,
            'ok' => 1
        ];

        if (in_array($label, array_keys($values))) {
            return $values[$label];
        }

        return $label;
    }

    /**
     * Get correctness data for a sentence.
     *
     * @param  int $sentenceId Sentence ID.
     *
     * @return array
     */
    public function getCorrectnessForSentence($sentenceId)
    {
        return $this->find()
            ->where(['sentence_id' => $sentenceId])
            ->select(['correctness', 'modified', 'dirty'])
            ->contain([
                'Users' => ['fields' => ['id', 'username']]
            ])
            ->all()
            ->toList();
    }

    /**
     * Make all sentences with given id dirty.
     *
     * @param  int $sentenceId ID of sentence to dirty. Should be sanitized.
     */
    public function makeDirty($sentenceId)
    {
        $this->updateAll(['dirty' => true], ['sentence_id' => $sentenceId]);
    }
}
