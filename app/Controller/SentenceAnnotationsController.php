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
 * Controller for contributions.
 *
 * @category Contributions
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceAnnotationsController extends AppController
{
    public $name = 'SentenceAnnotations';
    public $helpers = array(
        'SentenceAnnotations',
        'Pagination'
    );


    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'last_modified'
        );
    }


    /**
     * Index page. Doesn't do anything, just displays text to explain how it works.
     *
     * @return void
     */
    public function index()
    {
        $annotations = $this->SentenceAnnotation->getLatestAnnotations(20);
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
                    $this->request->data['SentenceAnnotation']['sentence_id']
                )
            );
        } else {
            $sentenceId = Sanitize::html($sentenceId);
            $result = $this->SentenceAnnotation->getAnnotationsForSentenceId(
                $sentenceId
            );

            $this->set('sentence', $result['Sentence']);
            $this->set('annotations', $result['SentenceAnnotation']);
        }
    }

    /**
     * Save annotation.
     *
     * @return void
     */
    public function save()
    {
        if (empty($this->request->data)) {
            return;
        }

        if (!isset($this->request->data['SentenceAnnotation']['id'])) {
            $this->SentenceAnnotation->create();
        }

        $sentenceId = Sanitize::paranoid(
            $this->request->data['SentenceAnnotation']['sentence_id']
        );
        $this->request->data['SentenceAnnotation']['user_id'] = CurrentUser::get('id');
        $text = trim($this->request->data['SentenceAnnotation']['text']);

        $annotation = array(
            'id'          => $this->request->data['SentenceAnnotation']['id'],
            'sentence_id' => $sentenceId,
            'meaning_id'  => $this->request->data['SentenceAnnotation']['meaning_id'],
            'text'        => $text,
            'user_id'     => CurrentUser::get('id'),
        );
        if ($this->SentenceAnnotation->save($annotation)) {
            $this->flash(
                'Index saved.',
                "/sentence_annotations/show/".$sentenceId
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
        $id = Sanitize::paranoid($id);
        $sentenceId = Sanitize::paranoid($sentenceId);

        if ($this->SentenceAnnotation->delete($id)) {
            $this->redirect(array("action" => "show", $sentenceId));
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
                array('action'=>'search', $this->request->data['SentenceAnnotation']['text'])
            );

        } else {

            $query = Sanitize::stripScripts($query);

            $annotations = null;
            if (trim($query) != '') {
                $annotations = $this->SentenceAnnotation->search($query);
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
        $textToReplace = $this->request->data['SentenceAnnotation']['textToReplace'];
        $textReplacing = $this->request->data['SentenceAnnotation']['textReplacing'];
        $newAnnotations = $this->SentenceAnnotation->replaceTextInAnnotations(
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
        $pagination = array(
            'SentenceAnnotation' => array(
                'fields' => array(
                    'sentence_id',
                    'modified',
                    'user_id',
                    'text'
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                ),
                'limit' => 200,
                'order' => 'modified DESC'
            )
        );

        $this->paginate = $pagination;
        $results = $this->paginate();

        //$annotations = $this->SentenceAnnotation->getLatestAnnotations(500);
        $this->set('annotations', $results);
    }
}
?>
