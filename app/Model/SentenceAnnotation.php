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
 * Model for sentence annotations.
 *
 * @category SentenceAnnotations
 * @package  Models
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceAnnotation extends AppModel
{
    public $belongsTo = array('Sentence', 'User');

    public $actsAs = array('Containable');


    /**
     * Get annotations for the sentence specified.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getAnnotationsForSentenceId($sentenceId)
    {
        return $this->Sentence->find(
            'first',
            array(
                'limit' => 10,
                'conditions' => array(
                    'Sentence.id' => $sentenceId
                ),
                'contain' => array('SentenceAnnotation')
            )
        );
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
        return $this->find(
            'all',
            array(
                'order' => 'modified DESC',
                'limit' => $limit,
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                )
            )
        );
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
        return $this->find(
            'all',
            array(
                'conditions' => array(
                    'SentenceAnnotation.text LIKE' => '%'.$query.'%'
                )
            )
        );
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

            $annotation['SentenceAnnotation']['text'] = preg_replace(
                "/$pattern/",
                $textReplacing,
                $annotation['SentenceAnnotation']['text']
            );

            $newAnnotations[] = $annotation;

            $this->id = $annotation['SentenceAnnotation']['id'];
            $data['text'] = $annotation['SentenceAnnotation']['text'];
            $data['user_id'] = CurrentUser::get('id');
            $this->save($data);
        }

        return $newAnnotations;
    }
}
