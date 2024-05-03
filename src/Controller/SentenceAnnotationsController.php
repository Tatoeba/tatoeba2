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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use App\Model\CurrentUser;

/**
 * Controller for contributions.
 *
 * @category Contributions
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class SentenceAnnotationsController extends AppController
{
    public $name = 'SentenceAnnotations';
    public $helpers = array(
        'SentenceAnnotations',
        'Pagination'
    );

    /**
     * Index page. Doesn't do anything, just displays text to explain how it works.
     *
     * @return void
     */
    public function index()
    {
        $annotations = $this->SentenceAnnotations->getLatestAnnotations(20);
        $this->set('annotations', $annotations);
    }

    /**
     * Display annotations for given sentence.
     *
     * @param int $sentenceId Id of sentence.
     *
     * @return void
     */
    public function show($sentenceId = null)
    {
        if ($sentenceId == null) {
            $this->redirect(
                array(
                    "action" => "show",
                    $this->request->getData('sentence_id')
                )
            );
        } else {
            $result = $this->SentenceAnnotations->getAnnotationsForSentenceId(
                $sentenceId
            );

            $this->set('sentence', $result);
            $this->set('annotations', $result->sentence_annotations);
        }
    }

    /**
     * Save annotation.
     *
     * @return void
     */
    public function save()
    {
        if (empty($this->request->getData())) {
            return;
        }
        
        $savedAnnotation = $this->SentenceAnnotations->saveAnnotation(
            $this->request->getData(), CurrentUser::get('id')
        );
        
        if ($savedAnnotation) {
            $sentenceId = $savedAnnotation->sentence_id;
            $this->flash(
                'Index saved.',
                '/sentence_annotations/show/'.$sentenceId
            );
        }
    }

    /**
     * Delete annotation from sentence.
     *
     * @param int $id         Id of annotation.
     * @param int $sentenceId Id of sentence.
     *
     * @return void
     */
    public function delete($id, $sentenceId)
    {
        $annotation = $this->SentenceAnnotations->get($id);
        if ($this->SentenceAnnotations->delete($annotation)) {
            $this->redirect(['action' => 'show', $sentenceId]);
        }
    }

    /**
     * Search annotations.
     *
     * @param string $query Seach query.
     *
     * @return void
     */
    public function search($query = null)
    {
        if ($query == null) {

            $this->redirect(
                ['action'=>'search', $this->request->getData('text')]
            );

        } else {

            $annotations = null;
            if (trim($query) != '') {
                $annotations = $this->SentenceAnnotations->search($query);
            }
            $this->set('query', $query);
            $this->set('annotations', $annotations);

        }
    }

    /**
     * Replace text in annotations by some other text.
     * TODO The replacement process needs optimization...
     *
     * @return void
     */
    public function replace()
    {
        $textToReplace = $this->request->getData('textToReplace');
        $textReplacing = $this->request->getData('textReplacing');
        $newAnnotations = $this->SentenceAnnotations->replaceTextInAnnotations(
            $textToReplace, $textReplacing
        );
        $this->set('textToReplace', $textToReplace);
        $this->set('textReplacing', $textReplacing);
        $this->set('annotations', $newAnnotations);
    }

    /**
     * Latest modifications.
     *
     * @return void
     */
    public function last_modified()
    {
        $pagination = [
            'fields' => [
                'sentence_id',
                'modified',
                'user_id',
                'text'
            ],
            'contain' => [
                'Users' => [
                    'fields' => ['username']
                ]
            ],
            'limit' => 200,
            'order' => ['modified' => 'DESC']
        ];

        $this->paginate = $pagination;
        $results = $this->paginate();

        $this->set('annotations', $results);
    }
}
