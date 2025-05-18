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

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use App\Model\CurrentUser;

class SentenceAnnotationsTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsto('Sentences');
        $this->belongsto('Users');

        $this->addBehavior('Timestamp');
    }

    /**
     * Get annotations for the sentence specified.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getAnnotationsForSentenceId($sentenceId)
    {
        return $this->Sentences->find()
            ->limit(10)
            ->where(['Sentences.id' => $sentenceId])
            ->contain(['SentenceAnnotations'])
            ->first();
    }


    /**
     * Get latest annotations. NOTE: Annotations are not logged. Here, we simply
     * display the annotations order by last modified first.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getLatestAnnotations($limit)
    {
        return $this->find()
            ->order(['modified' => 'DESC'])
            ->limit($limit)
            ->contain([
                'Users' => ['fields' => ['username']]
            ])
            ->toList();
    }


    /**
     * Get annotations for the sentence specified.
     *
     * @param string $query Search query.
     *
     * @return array
     */
    public function search($query)
    {
        $query = preg_replace("/<space>/", " ", $query);
        return $this->find()
            ->where(['SentenceAnnotations.text LIKE' => '%'.$query.'%'])
            ->contain(['Sentences'])
            ->toList();
    }

    /**
     * Replace text in results of a search by some other text.
     *
     * @param string $textToReplace Text to replace.
     * @param string $textReplacing Text that will replace previous text.
     *
     * @return array
     */
    public function replaceTextInAnnotations($textToReplace, $textReplacing)
    {
        $textToReplace = preg_replace("/<space>/", " ", $textToReplace);
        $annotations = $this->search($textToReplace);
        $newAnnotations = array();

        foreach ($annotations as $annotation) {
            $pattern = quotemeta($textToReplace);
            $pattern = preg_replace("/\|/", "\\|", $pattern);
                // because the character | is not taken into account in quotemeta()

            $annotation->text = preg_replace(
                "/$pattern/",
                $textReplacing,
                $annotation->text
            );

            $newAnnotations[] = $annotation;

            $annotation->user_id = CurrentUser::get('id');
            $this->save($annotation);
        }

        return $newAnnotations;
    }


    /**
     * Save annotation.
     */
    public function saveAnnotation($data, $currentUserId)
    {
        if (isset($data['id'])) {
            $annotation = $this->get($data['id']);
        } else {
            $annotation = $this->newEntity();
        }       
        
        $annotation->sentence_id = $data['sentence_id'];
        $annotation->meaning_id = $data['meaning_id'];
        $annotation->text = trim($data['text']);
        $annotation->user_id = $currentUserId;

        return $this->save($annotation);
    }
}
