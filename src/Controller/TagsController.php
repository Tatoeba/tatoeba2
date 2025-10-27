<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\Query;
use App\Model\CurrentUser;


class TagsController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Tags';
    public $components = ['CommonSentence', 'Flash'];
    public $helpers = ['Pagination'];
    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->Security->unlockedActions = [
            'add_tag_post'
        ];

        return parent::beforeFilter($event);
    }

    /**
     * Add a tag to a sentence
     *
     * @return void
     */

    public function add_tag_post()
    {
        if ($this->request->is('ajax')) {
            $this->helpers[] = 'Tags';

            $tagName = $this->request->getData('tag_name');
            $sentenceId = $this->request->getData('sentence_id');
            $userId = CurrentUser::get("id");
            $username = CurrentUser::get("username");
            $tag = $this->Tags->addTag($tagName, $userId, $sentenceId);

            $isSaved = $tag && $tag->link && !$tag->link->alreadyExists;
            $this->set('isSaved', $isSaved);
            if ($isSaved) {
                $this->set('tagName', $tag->name);
                $this->set('tagId', $tag->id);
                $this->set('userId', $userId);
                $this->set('username', $username);
                $this->set('sentenceId', $sentenceId);
                $this->set('date', $tag->link->added_time);
                $this->loadModel('Sentences');
                $sentence = $this->Sentences->get($sentenceId, ['fields' => ['lang']]);
                $this->set('sentenceLang', $sentence->lang);
            }
        }
    }

    /**
     * Display list of tags.
     *
     * @param String $filter Filters the tags list with only those that contain the
     *                       search string.
     */
    public function view_all($filter = null)
    {
        $this->helpers[] = 'Tags';

        $conditions = [];
        if (!empty($filter)) {
            $conditions = [
                'name LIKE' => "%$filter%"
            ];
        }
        $this->paginate = [
            'limit' => 50,
            'fields' => ['name', 'id', 'nbrOfSentences'],
            'order' => ['nbrOfSentences' => 'DESC', 'id' => 'ASC'],
            'conditions' => $conditions,
            'sort' => $this->request->getQuery('sort', 'nbrOfSentences'),
            'direction' => $this->request->getQuery('direction', 'desc'),
        ];

        $allTags = $this->paginate();
        $this->set("allTags", $allTags);
        $this->set("filter", $filter);
    }

    /**
     * Remove a tag from a sentence when on the sentence page
     *
     * @param int $tagId      Id of the tag to remove from the sentence
     * @param int $sentenceId Id of the sentence to remove the tag from
     *
     * @return void
     */

    public function remove_tag_from_sentence($tagId, $sentenceId)
    {
        if (!empty($tagId) && !empty($sentenceId)) {
            $this->Tags->removeTagFromSentence($tagId, $sentenceId);
        }
        return $this->redirect([
            'controller' => 'sentences',
            'action' => 'show',
            $sentenceId
        ]);

    }


    /**
     * Remove a tag from a sentence when on the "show all sentences with
     * this tag" page
     *
     * @param int $tagId      Id of the tag to remove from this sentence
     * @param int $sentenceId Id of the sentence to remove the tag from
     *
     * @return void
     */
    public function remove_tag_of_sentence_from_tags_show($tagId, $sentenceId)
    {
        if (!empty($tagId) && !empty($sentenceId)) {
            $this->Tags->removeTagFromSentence($tagId, $sentenceId);
        }
        return $this->redirect($this->referer());
    }


    /**
     * Display a list of all sentences with a given tag
     *
     * @param string $tagId           Id of the tag
     * @param string $lang            Filter only sentences in this language.
     *
     * @return void
     */
    public function show_sentences_with_tag($tagId = null, $lang = null)
    {
        // In case the $tagId is not an int we assume that the user
        // comes from an old URL with the internal name, so we
        // redirect them to the right URL.
        if ($tagId && $tagId != '0' && intval($tagId) == 0) {
            $actualTagId = $this->Tags->getIdFromInternalName($tagId);
            return $this->redirect(
                [
                    "controller" => "tags",
                    "action" => "show_sentences_with_tag",
                    $actualTagId, $lang
                ],
                301
            );
        }

        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Tags';

        $tagName = $this->Tags->getNameFromId($tagId);
        $tagExists = !empty($tagName);
        $this->set('tagExists', $tagExists);
        $this->set('tagId', $tagId);

        if ($tagExists) {
            $this->loadModel('Sentences');
            $this->loadModel('TagsSentences');
            $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
            $options = [
                'conditions' => ['tag_id' => $tagId],
                'maxResults' => $totalLimit,
                'contain' => [
                    'Sentences' => function (Query $q) {
                        return $q
                          ->find('filteredTranslations')
                          ->find('hideFields')
                          ->contain($this->Sentences->contain(['translations' => true]))
                          ->select($this->Sentences->fields());
                    },
                ],
            ];
            $total = $this->TagsSentences->find()->where(['tag_id' => $tagId]);
            if (!empty($lang) && $lang != 'und') {
                $options['conditions']['Sentences.lang'] = $lang;
                $total->matching('Sentences', function (Query $q) use ($lang) {
                    return $q->where(['Sentences.lang' => $lang]);
                });
            }

            $this->paginate = [
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'sort' => $this->request->getQuery('sort', 'id'),
                'direction' => $this->request->getQuery('direction', 'desc'),
                // keep added_time for backward compatibility
                'sortWhitelist' => ['id', 'sentence_id', 'added_time'],
            ];
            $finder = ['latest' => $options];
            try {
                $sentences = $this->paginate($this->TagsSentences, compact('finder'));
            } catch (\Cake\Http\Exception\NotFoundException $e) {
                return $this->redirectPaginationToLastPage();
            }
            $total = $total->count();

            $taggerIds = [];
            foreach ($sentences as $sentence) {
                $taggerIds[] = $sentence->user_id;
            }

            $this->set('langFilter', $lang);
            $this->set('allSentences', $sentences);
            $this->set('tagName', $tagName);
            $this->set('taggerIds', $taggerIds);
            $this->set(compact('total', 'totalLimit'));
        } else {
            $this->Flash->set(
                __(
                    'There are no sentences for this tag. The tag you are looking '.
                    'for has been deleted or does not exist.', true
                )
            );
            return $this->redirect([
                'controller' => 'tags',
                'action' => 'view_all'
            ]);
        }
    }

    public function search()
    {
        $search = $this->request->getData('search');
        return $this->redirect([
            'controller' => 'tags',
            'action' => 'view_all',
            $search
        ]);
    }

    public function autocomplete($search)
    {
        $allTags = $this->Tags->Autocomplete($search);

        $this->loadComponent('RequestHandler');
        $this->set('results', $allTags);
        $this->set('_serialize', ['results']);
        $this->RequestHandler->renderAs($this, 'json');
    }
}
