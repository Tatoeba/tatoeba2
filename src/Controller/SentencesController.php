<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
use App\Model\CurrentUser;
use App\Lib\LanguagesLib;
use App\Lib\SphinxClient;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Cake\View\ViewBuilder;
use Exception;

/**
 * Controller for sentences.
 *
 * @category Sentences
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class SentencesController extends AppController
{
    public $name = 'Sentences';
    public $components = array (
        'LanguageDetection',
        'CommonSentence',
        'Permissions',
    );
    public $helpers = array(
        'Sentences',
        'Menu',
        'Lists',
        'SentenceButtons',
        'Html',
        'Logs',
        'Pagination',
        'Comments',
        'Navigation',
        'Languages',
        'CommonModules'
    );
    public $paginate = array(
        'limit' => 100,
        "order" => "Sentence.modified DESC"
    );

    public $uses = array(
        'Audio',
        'Sentence',
        'SentencesSentencesLists',
        'SentencesList',
        'User',
        'UsersLanguages',
        'Tag',
        'UsersSentences',
        'Vocabulary'
    );

    private $defaultSearchCriteria = array(
        'query' => '',
        'from' => 'und',
        'to' => 'und',
        'tags' => '',
        'list' => '',
        'user' => '',
        'orphans' => 'no',
        'unapproved' => 'no',
        'native' => '',
        'has_audio' => '',
        'trans_to' => 'und',
        'trans_link' => '',
        'trans_user' => '',
        'trans_orphan' => '',
        'trans_unapproved' => '',
        'trans_has_audio' => '',
        'trans_filter' => 'limit',
        'sort' => 'relevance',
        'sort_reverse' => '',
    );

    public function initialize() {
        parent::initialize();

        $params = $this->request->params;
        $noCsrfActions = ['edit_sentence', 'change_language'];
        if (in_array($params['action'], $noCsrfActions)) {
            $this->components()->unload('Csrf');
        }
    }

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
          'add_an_other_sentence',
          'save_translation',
          'change_language',
          'edit_sentence'
        ]);

        return parent::beforeFilter($event);
    }

    /**
     * Redirects to a random sentence.
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(
            array(
                "action" => "show",
                "random"
            )
        );
    }

    /**
     * Show sentence of specified id (or a random one if no id specified).
     *
     * @param mixed $id Id of the sentence or language of the random sentence.
     *
     * @return void
     */
    public function show($id = null)
    {
        $this->helpers[] = 'Tags';
        $this->helpers[] = 'Messages';
        $this->helpers[] = 'Lists';
        $this->helpers[] = 'Members';
        $this->helpers[] = 'Audio';

        if ($id == "random" || $id == null || $id == "" ) {
            $id = $this->request->getSession()->read('random_lang_selected');
        }

        if (is_numeric($id)) {

            // ----- if we give directly an id -----
            // Whether the sentence still exists or not, we retrieve the
            // contributions and the comments because we don't want them
            // to disappear just because the sentence was deleted.
            $contributions = $this->Sentences->getContributionsRelatedToSentence(
                $id
            );

            $comments = $this->Sentences->getCommentsForSentence($id);
            $commentsPermissions = $this->Permissions->getCommentsOptions($comments);

            $this->set('sentenceComments', $comments);
            $this->set('commentsPermissions', $commentsPermissions);
            $this->set('contributions', $contributions);

            // And now we retrieve the sentence
            $sentence = $this->Sentences->getSentenceWithId($id);

            $canComment = CurrentUser::isMember()
                && (!empty($contributions) || !empty($sentence));
            $this->set('canComment', $canComment);

            // this way "next" and "previous"
            $lang = $this->request->getSession()->read('random_lang_selected');
            $neighbors = $this->Sentences->getNeighborsSentenceIds($id, $lang);
            $this->set('nextSentence', $neighbors['next']);
            $this->set('prevSentence', $neighbors['prev']);

            $this->loadModel('UsersSentences');
            $correctnessArray = $this->UsersSentences->getCorrectnessForSentence($id);
            $this->set('correctnessArray', $correctnessArray);

            // If no sentence, we don't need to go further.
            // We just set some variable so we don't get warnings.
            if ($sentence == null) {
                $this->set('sentenceId', $id);
                $this->set('tagsArray', array());
                return;
            }

            $tagsArray = $this->Sentences->getAllTagsOnSentence($id);
            $this->loadModel('SentencesSentencesLists');
            $listsArray = $this->SentencesSentencesLists->getListsForSentence($id);

            $this->set('sentence', $sentence);

            $this->set('tagsArray', $tagsArray);
            $this->set('listsArray', $listsArray);

        } else {
            // ----- if we want a random sentence in a specific language -----
            // here only to make things clearer as "id" is not a number
            if (array_key_exists($id, LanguagesLib::languagesInTatoeba())) {
                $lang = $id;
                $this->addLastUsedLang($lang);
            } else {
                $lang = null;
            }

            $randomId = $this->Sentences->getRandomId($lang);

            if (is_null($randomId)) {
                $searchDisabled = !Configure::read('Search.enabled');
                if ($searchDisabled) {
                    $this->set('searchProblem', 'disabled');
                } else {
                    $this->set('searchProblem', 'error');
                }
            } else {
                $this->request->getSession()->write('random_lang_selected', $lang);
                $this->redirect(array("action"=>"show", $randomId));
            }

        }
    }


    /**
     * Display sentence of specified id.
     *
     * @return void
     */

    public function go_to_sentence()
    {
        $id = intval($this->request->getQuery('sentence_id'));
        if ($id == 0) {
            $id = 'random';
        }
        $this->redirect(array("action"=>"show", $id));
    }

    /**
     * Add a new sentence.
     *
     * @return void
     */

    public function add()
    {
    }

    /**
     * Delete a sentence.
     *
     * @param int $id Id of the sentence.
     *
     * @return void
     */
    public function delete($id)
    {
        $isDeleted = $this->Sentences->deleteSentence($id);
        if ($isDeleted) {
            $flashMessage = format(
                __('The sentence #{id} has been deleted.'),
                array('id' => $id)
            );

        } else {
            $flashMessage = format(
                __('Error: the sentence #{id} could not be deleted.'),
                array('id' => $id)
            );
        }
        $this->flash(
            $flashMessage,
            array('controller' => 'sentences', 'action' => 'show', $id)
        );
    }

    /**
     * used by sentences.contribute.js
     * save an other new sentence
     *
     * @return void
     */
    public function add_an_other_sentence()
    {
        $userId = $this->Auth->user('id');
        $userLevel = $this->Sentences->Users->getLevelOfUser($userId);
        if ($userLevel < 0) {
            return;
        }

        $sentenceLang = $this->request->getData('selectedLang');
        $sentenceText = $this->request->getData('value');

        if (is_null($sentenceText) || is_null($sentenceLang)) {
            //TODO add error handling
            return;
        }

        $userName = $this->Auth->user('username');
        $sentenceLicense = $this->request->getData('sentenceLicense');

        $savedSentence = $this->CommonSentence->addNewSentence(
            $sentenceLang,
            $sentenceText,
            $userId,
            $userName,
            0,
            $sentenceLicense
        );

        // saving
        if ($savedSentence) {
            $sentence = $this->Sentences->getSentenceWithId($savedSentence->id);
            $this->set('duplicate', $savedSentence->isDuplicate);
            $this->set('sentence', $sentence);
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->loadComponent('RequestHandler');
            $this->set('_serialize', ['sentence']);
            $this->RequestHandler->renderAs($this, 'json');
        }
    }

    /**
     * Edit sentence. Used by AJAX request in sentences.edit_in_place.js.
     *
     * @return void
     */
    public function edit_sentence()
    {
        $acceptsJson = $this->request->accepts('application/json');
        $sentence = $this->Sentences->editSentence($this->request->data);
        if ($acceptsJson) {
            $sentence->dir = LanguagesLib::getLanguageDirection($sentence->lang);
            $this->set('result', $sentence);
            $this->viewBuilder()->setLayout('json');
            $this->render('/Generic/json');
        } else {
            if (empty($sentence)) {
                // TODO Better error handling.
                $this->redirect(array('controller' => 'pages', 'action' => 'home'));
            } else {
                $this->viewBuilder()->setLayout('ajax');
                $this->set('sentence_text', $sentence->text);
            }
        }
    }

    /**
     * Adopt a sentence. User can modify sentence and becomes
     * responsible of the sentence.
     *
     * @param int $id Id of the sentence.
     *
     * @return void
     */
    public function adopt($id)
    {
        $userId = $this->Auth->user('id');

        $this->Sentences->setOwner($id, $userId, CurrentUser::get('role'));

        $this->renderAdopt($id);
    }

    /**
     * Let go a sentence. Sentence does not belong to user anymore,
     * i.e. user cannot modify it anymore, and is not responsible
     * of it either.
     *
     * @param int $id Id of the sentence.
     *
     * @return void
     */
    public function let_go($id)
    {
        $userId = $this->Auth->user('id');

        $this->Sentences->unsetOwner($id, $userId);

        $this->renderAdopt($id);
    }

    private function renderAdopt($id)
    {
        $acceptsJson = $this->request->accepts('application/json');
        $sentence = $this->Sentences->get($id, [
            'contain' => ['Users' => ['fields' => ['username']]]
        ]);

        if ($acceptsJson) {
            $this->loadComponent('RequestHandler');
            $this->set('user', $sentence->user);
            $this->set('_serialize', ['user']);
            $this->RequestHandler->renderAs($this, 'json');
        } else {
            $this->set('sentenceId', $id);
            $this->set('owner', $sentence->user);
            $this->viewBuilder()->setLayout('ajax');
            $this->render('adopt');
        }
    }


    /**
     * Save the translation.
     *
     * @return void
     */
    public function save_translation()
    {
        $sentenceId = $this->request->getData('id');
        $translationLang = $this->request->getData('selectLang');
        $userId = $this->Auth->user('id');
        $userLevel = $this->Sentences->Users->getLevelOfUser($userId);

        if ($userLevel < 0) {
            return ;
        }

        $translationText = $this->request->getData('value');

        // Store selected lang in cookie as default language for drop-downs
        $this->Cookie->write('contribute_lang', $translationLang, false, "+1 month");

        if (isset($translationText)
            && trim($translationText) != ''
            && isset($sentenceId)
            && !(empty($userId))
        ) {
            // Language detection
            if ($translationLang == 'auto') {
                $translationLang = $this->LanguageDetection->detectLang(
                    $translationText,
                    $this->Auth->user('username')
                );
            }

            // Saving...
            $sentenceLang = $this->Sentences->getLanguageCodeFromSentenceId($sentenceId);
            $translation = $this->Sentences->saveTranslation(
                $sentenceId,
                $sentenceLang,
                $translationText,
                $translationLang
            );

            if ($translation) {
                $this->set('translation', $translation);
                $this->set('parentId', $sentenceId);
            }
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->set('result', $translation);
            $this->viewBuilder()->setLayout('json');
            $this->render('/Generic/json');
        }
    }

    private function _find_sphinx_markers($query)
    {
        // TODO take into account escaped sequences
        $sphinx_markers = array('"', '/', '$', '^', '-', '|', '!');
        $result = array();
        foreach ($sphinx_markers as $marker) {
            if (strpos($query, $marker) !== false) {
                $result[] = $marker;
            }
        }
        return $result;
    }

    /**
     * Search sentences.
     *
     * @return void
     */
    public function search()
    {
        if (!Configure::read('Search.enabled')) {
            $this->render('search_disabled');
            return;
        }

        $criteriaVars = array();
        foreach ($this->defaultSearchCriteria as $name => $default) {
            $criteriaVars[$name] = $this->request->getQuery($name, $default);
        }
        extract($criteriaVars);
        $ignored = array();

        /* Convert simple search to advanced search parameters */
        if (!is_null($this->request->getQuery('to'))
            && is_null($this->request->getQuery('trans_to'))) {
            $trans_to = $to;
        }

        // Disallow this currently impossible combination
        if (!empty($native) && $from == 'und') {
            $ignored[] = __(
                /* @translators: This string will be preceded by “Warning: the
                   following criteria have been ignored:” */
                "“owned by a self-identified native”, because “sentence ".
                "language” is set to “any”",
                true
            );
            $native = '';
        }

        // Session variables for search bar
        $this->request->getSession()->write('search_from', $from);
        $this->request->getSession()->write('search_to', $to);
        $this->addLastUsedLang($from);
        $this->addLastUsedLang($to);

        // replace strange space
        $query = str_replace(
            array('　', ' '),
            ' ',
            $query
        );

        $sphinx = $this->Sentences->sphinxOptions($query, $from, $sort, $sort_reverse);

        $transFilter = array();
        // if we want to search only on sentences having translations
        // in a specified language
        if ($trans_to !== 'und') {
            if ($trans_to && LanguagesLib::languageExists($trans_to)) {
                $transFilter[] = "t.l='$trans_to'";
            }
        }
        if (!empty($trans_link)) {
            $link = $trans_link == 'direct' ? 1 : 2;
            $transFilter[] = "t.d=$link";
        }
        if (!empty($trans_user)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($trans_user, ['fields' => ['id']])->first();
            if ($result) {
                $transFilter[] = 't.u='.$result['id'];
                if ($trans_orphan == 'yes') {
                    $ignored[] = format(
                        /* @translators: This string will be preceded by
                           “Warning: the following criteria have been
                           ignored:” */
                        __("“translation is orphan”, because “translation ".
                           "owner” is set to a username", true)
                    );
                    $trans_orphan = '';
                }
            } else {
                $ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been ignored:” */
                    __("“translation owner”, because “{username}” is not ".
                       "a valid username", true),
                    array('username' => $trans_user)
                );
                $trans_user = '';
            }
        }
        if (!empty($trans_orphan) && empty($trans_user)) {
            $op = $trans_orphan == 'yes' ? '=' : '<>';
            $transFilter[] = "t.u${op}0";
        }
        if (!empty($trans_unapproved)) {
            $correctness = $trans_unapproved == 'yes' ? 0 : 1;
            $transFilter[] = "t.c=$correctness";
        }
        if (!empty($trans_has_audio)) {
            $audio = $trans_has_audio == 'yes' ? 1 : 0;
            $transFilter[] = "t.a=$audio";
        }
        if ($transFilter || $trans_filter == 'exclude') {
            if (!$transFilter) {
                $transFilter = array(1);
            }
            $filter = implode(' & ', $transFilter);
            $sphinx['select'] = "*, ANY($filter FOR t IN trans) as filter";
            $filtering = $trans_filter == 'limit' ? 1 : 0;
            $sphinx['filter'][] = array('filter', $filtering);
        }

        // filter by user
        $user_id = null;
        if (!empty($user)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($user, ['fields' => ['id']])->first();
            if ($result) {
                $user_id = $result['id'];
                $sphinx['filter'][] = array('user_id', $user_id);
                if ($orphans == 'yes') {
                    $ignored[] = format(
                        /* @translators: This string will be preceded by
                           “Warning: the following criteria have been
                           ignored:” */
                        __("“sentence is orphan”, because “sentence ".
                           "owner” is set to a username", true)
                    );
                    $orphans = '';
                }
            } else {
                $ignored[] = format(
                    /* @translators: This string will be preceded by “Warning:
                       the following criteria have been ignored:” */
                    __("“sentence owner”, because “{username}” is not a ".
                       "valid username", true),
                    array('username' => $user)
                );
                $user = '';
            }
        }

        // filter by tags
        if (!empty($tags)) {
            $this->loadModel('Tags');
            $tagsArray = explode(',', $tags);
            $tagsArray = array_map('trim', $tagsArray);
            $result = $this->Tags->find()
                ->where(['name IN' => $tagsArray])
                ->select(['id', 'name'])
                ->toList();
            $tagsById = Hash::combine($result, '{n}.id', '{n}.name');
            if ($tagsById) {
                foreach (array_keys($tagsById) as $id)
                    $sphinx['filter'][] = array('tags_id', $id);
            }

            // clean provided list
            $unsetTags = array();
            foreach ($tagsArray as $i => $name) {
                if (!in_array($name, $tagsById)) {
                    $unsetTags[] = $tagsArray[$i];
                    unset($tagsArray[$i]);
                }
            }
            if ($unsetTags) {
                foreach ($unsetTags as $tagName) {
                    $ignored[] = format(
                        /* @translators: This string will be preceded by
                           “Warning: the following criteria have been
                           ignored:” */
                        __("“tagged as {tagName}”, because it's an invalid ".
                           "tag name", true),
                        compact('tagName')
                    );
                }
            }
            $tags = implode(',', $tagsArray);
        }

        // filter by list
        $this->loadModel('SentencesLists');
        $searchableLists = $this->SentencesLists->getSearchableLists();
        if (!empty($list)) {
            $isSearchable = $this->SentencesLists->isSearchableList($list);
            if ($isSearchable) {
                $sphinx['filter'][] = array('lists_id', $list);
                $found = false;
                foreach ($searchableLists as $rec) {
                    if ($list == $rec['id']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $searchableLists[] = $isSearchable;
                }
            } else {
                $ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been
                       ignored:” */
                    __("“belongs to list number {listId}”, because list ".
                       "{listId} is private or does not exist", true),
                    array('listId' => $list)
                );
                $list = '';
            }
        }

        // filter orphans
        if (!empty($orphans) && empty($user)) {
            $exclude_orphans = $orphans == 'no';
            $sphinx['filter'][] = array('user_id', 0, $exclude_orphans);
        }

        // filter unapproved
        if (!empty($unapproved)) {
            $exclude_unappr = $unapproved == 'no';
            // See the indexation SQL request for the value 127
            $sphinx['filter'][] = array('ucorrectness', 127, $exclude_unappr);
        }

        // filter self-identified natives
        if (!empty($native)) {
            $this->loadModel('UsersLanguages');
            $natives = $this->UsersLanguages->find()
                ->where([
                    'language_code' => $from,
                    'level' => 5,
                ])
                ->select(['of_user_id'])
                ->toList();
            $natives = Hash::extract($natives, '{n}.of_user_id');
            if ($natives) {
                if ($user_id && !in_array($user_id, $natives)) {
                    $ignored[] = format(
                        /* @translators: This string will be preceded by
                           “Warning: the following criteria have been
                           ignored:” */
                        __("“owned by a self-identified native”, because the ".
                           "criterion “owned by: {username}” is set whereas ".
                           "he or she is not a self-identified native in the ".
                           "language you're searching into",
                           true),
                        array('username' => $user)
                    );
                    $native = '';
                } else {
                    $sphinx['filter'][] = array('user_id', $natives);
                }
            }
        }

        // filter audio
        if (!empty($has_audio)) {
            $audio = $has_audio == 'yes' ? 1 : 0;
            $sphinx['filter'][] = array('has_audio', $audio);
        }

        $limit = CurrentUser::getSetting('sentences_per_page');
        $sphinx['page'] = $this->request->query('page');
        $sphinx['limit'] = $limit;

        $model = 'Sentences';
        if (CurrentUser::isMember()) {
            $contain = $this->Sentences->contain();
        } else {
            $contain = $this->Sentences->minimalContain();
        }
        $pagination = [
            'finder' => ['withSphinx' => [
                'translationLang' => $to,
                'nativeMarker' => CurrentUser::getSetting('native_indicator')
            ]],
            'fields' => $this->Sentences->fields(),
            'contain' => $contain,
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'sphinx' => $sphinx,
            'search' => $query
        ];

        $this->paginate = $pagination;
        try {
            $results = $this->paginate($model);
            $real_total = $this->Sentences->getRealTotal();
            $results = $this->Sentences->addHighlightMarkers($results);
        } catch (Exception $e) {
            $sphinx_markers = $this->_find_sphinx_markers($query);
            if (!empty($sphinx_markers)) {
                $this->set(compact('sphinx_markers'));
            }
        }

        $strippedQuery = preg_replace('/"|=/', '', $query);
        $this->loadModel('Vocabulary');
        $vocabulary = $this->Vocabulary->findByText($strippedQuery);

        $this->set('vocabulary', $vocabulary);
        $this->set('searchableLists', $searchableLists);
        $this->set(compact(array_keys($this->defaultSearchCriteria)));
        $this->set(compact('real_total', 'search_disabled', 'ignored', 'results'));
        $this->set(
            'is_advanced_search',
            !is_null($this->request->getQuery('trans_to'))
        );
    }

    public function advanced_search() {
        $this->loadModel('SentencesLists');
        $this->set('searchableLists', $this->SentencesLists->getSearchableLists());
        $this->set($this->defaultSearchCriteria);
    }

    /**
     * Show all sentences in a specific language
     *
     * @param string $lang              Show all sentences in this language.
     * @param string $translationLang   Show only translations into this lang.
     * @param string $notTranslatedInto Show only sentences which have no direct
     *                                  translation into this language.
     * @param string $filterAudioOnly   Show only sentences that have an mp3
     *
     * @return void
     */
    public function show_all_in($lang, $translationLang) {
        $this->helpers[] = 'ShowAll';

        if ($lang == 'unknown') {
            $conditions = ['Sentences.lang IS' => null];
        } else {
            $conditions = ['Sentences.lang' => $lang];
        }

        $this->addLastUsedLang($lang);
        $this->addLastUsedLang($translationLang);

        $pagination = [
            'finder' => ['filteredTranslations' => [
                'translationLang' => $translationLang,
                'nativeMarker' => CurrentUser::getSetting('native_indicator')
            ]],
            'fields' => $this->Sentences->fields(),
            'contain' => $this->Sentences->paginateContain($translationLang),
            'conditions' => $conditions,
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'order' => ['Sentences.id' => 'DESC']
        ];

        $this->paginate = $pagination;

        $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
        $allSentences = $this->paginateLatest($this->Sentences, $totalLimit);

        $total = $this->Sentences->find()->where($conditions)->count();

        $this->set('lang', $lang);
        $this->set('translationLang', $translationLang);
        $this->set('results', $allSentences);
        $this->set('total', $total);
        $this->set('totalLimit', $totalLimit);

        $this->Cookie->write('browse_sentences_in_lang', $lang, false, "+1 month");
        $this->Cookie->write('show_translations_into_lang', $translationLang, false, "+1 month");
        $this->render(null);
    }

    /**
     * Show random sentence.
     *
     * @param string $lang Language of the random sentence.
     *
     * @return void
     */
    public function random($lang = null)
    {
        if ($lang == null) {
            $lang = $this->request->getSession()->read('random_lang_selected');
        }

        $randomId = $this->Sentences->getRandomId($lang);

        if (is_null($randomId)) {
            $this->set('searchProblem', true);
            $randomSentence = null;
        } else {
            $randomSentence = $this->Sentences->getSentenceWithId($randomId);
        }

        $this->request->getSession()->write('random_lang_selected', $lang);
        $this->set('random', $randomSentence);

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->loadComponent('RequestHandler');
            $this->set('_serialize', ['random']);
            $this->RequestHandler->renderAs($this, 'json');
        }
    }


    /**
     * Show all the sentences of a given user
     *
     * @param string $userName The user's name.
     * @param string $lang     Filter only sentences in this language.
     *
     * @return void
     */
    public function of_user($userName, $lang = null)
    {
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUserName($userName);

        // if there's no such user no need to do more computation
        $this->set("userName", $userName);
        if (empty($userId)) {
            $this->set("userExists", false);
            return;
        }

        $this->set("userExists", true);

        $onlyOriginal = array_key_exists('only_original', $this->request->getQueryParams());
        $this->set('onlyOriginal', $onlyOriginal);

        $this->paginate = array(
            'Sentences' => array(
                'fields' => array(
                    'id',
                    'text',
                    'lang',
                    'user_id',
                    'correctness'
                ),
                'contain' => array(
                    'Transcriptions' => array(
                        'Users' => array('fields' => array('username')),
                    ),
                    'Users' => array(
                        'fields' => array('username')
                    )
                ),
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'order' => ['Sentences.modified' => 'DESC']
            )
        );

        $conditions = $this->Sentences->find()->where(['user_id' => $userId]);
        // if the lang is specified then we also filter on the language
        if (!empty($lang)) {
            $conditions = $conditions->where(['lang' => $lang]);
        }

        if ($onlyOriginal) {
            $conditions = $conditions->where('based_on_id = 0');
        }

        try {
            $sentences = $this->paginate($conditions);
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }

        $this->set('user_sentences', $sentences);
        $this->set("lang", $lang);
    }


    /**
     * Change language of a sentence.
     * Used in AJAX request in sentences.change_language.js.
     *
     * @return void
     */
    public function change_language()
    {
        if (isset($this->request->data['id'])
            && isset($this->request->data['newLang'])
        ) {
            $newLang = $this->request->data['newLang'];
            $id = $this->request->data['id'];

            $lang = $this->Sentences->changeLanguage($id, $newLang);
            $this->loadModel('UsersSentences');
            $this->UsersSentences->makeDirty($id);
            $this->set('lang', $lang);
        }
    }

    /**
     * Use by ajax only, get previous and next valid sentence id
     *
     * @param int    $id   Id of the current sentence.
     * @param string $lang Previous and next in this language.
     *
     * @return void
     */
    public function get_neighbors_for_ajax($id, $lang)
    {
        $this->request->getSession()->write('random_lang_selected', $lang);
        $neighbors = $this->Sentences->getNeighborsSentenceIds($id, $lang);
        $this->set('result', $neighbors);
        $this->viewBuilder()->setLayout('json');
    }


    /**
     * action uses to display the import forms
     *
     * @TODO maybe move this in pages controller
     *
     * @return void
     */
    public function import()
    {
        if (!CurrentUser::isAdmin()) {
            $this->redirect(
                array(
                    "controller" => "pages",
                    "action" => "index",
                )
            );
        }
    }


    /**
     * Sentences with audio.
     *
     * @param string $lang Language of the sentences.
     *
     * @return void
     */
    public function with_audio($lang = null)
    {
        $this->redirect(array(
            'controller' => 'audio',
            'action' => 'index',
            $lang
        ));
    }

    /**
     * Edit correctness of a sentence.
     *
     * @return void
     */
    public function edit_correctness()
    {
        $sentenceId = $this->request->getData('id');
        $correctness = $this->request->getData('correctness');

        if (CurrentUser::isModerator()) {
            $this->Sentences->editCorrectness($sentenceId, $correctness);
            $this->redirect(
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $sentenceId
                )
            );
        } else {
            $this->redirect(
                array(
                    "controller" => "pages",
                    "action" => "home",
                )
            );
        }
    }


    public function edit_audio()
    {
        $sentenceId = $this->request->getData('id');
        $ownerName = $this->request->getData('ownerName');
        $hasaudio = $this->request->getData('hasaudio');

        if (CurrentUser::isAdmin()) {
            $this->loadModel('Audios');
            if ($hasaudio) {
                $this->Audios->assignAudioTo($sentenceId, $ownerName);
            } else {
                $audioToDelete = $this->Audios->find()
                    ->where(['sentence_id' => $sentenceId])
                    ->first();
                $this->Audios->delete($audioToDelete);
            }
            $this->redirect(
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $sentenceId
                )
            );
        } else {
            $this->redirect(
                array(
                    "controller" => "pages",
                    "action" => "home",
                )
            );
        }
    }

    public function edit_license()
    {
        $sentenceId = $this->request->getData('id');
        $newLicense = $this->request->getData('license');
        if (is_null($sentenceId) || is_null($newLicense)) {
            throw new \Cake\Http\Exception\BadRequestException();
        }

        $sentence = $this->Sentences->get($sentenceId);
        $sentence = $this->Sentences->patchEntity($sentence, ['license' => $newLicense]);

        if (!CurrentUser::canEditLicenseOfSentence($sentence)) {
            $this->Flash->set(__('You are not allowed to change the license of this sentence.'));
        } else {
            $errors = $sentence->getError('license');
            $savedSentence = $this->Sentences->save($sentence);
            if ($savedSentence) {
                $this->Flash->set(format(
                    __('The license of the sentence has been changed to “{newLicense}”.'),
                    compact('newLicense')
                ));
            } elseif (!empty($errors)) {
                $message = format(
                    __('Unable to change the license to “{newLicense}” because:'),
                    compact('newLicense')
                );
                $params = compact('errors');
                $this->Flash->set($message, compact('params'));
            }
        }

        $this->redirect(array(
            'action' => 'show',
            $sentenceId
        ));
    }
}
