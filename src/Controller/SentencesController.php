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
 * @link     http://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use App\Lib\LanguagesLib;
use App\Lib\SphinxClient;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Controller for sentences.
 *
 * @category Sentences
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
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
        'sort' => 'words',
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
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array(
            'index',
            'show',
            'search',
            'advanced_search',
            'of_user',
            'random',
            'go_to_sentence',
            'several_random_sentences',
            'get_neighbors_for_ajax',
            'show_all_in',
            'with_audio'
        );

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
        $id = intval($this->request->query['sentence_id']);
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

        if (!isset($_POST['value'])
            || !isset($_POST['selectedLang'])
        ) {
            //TODO add error handling
            return;
        }

        $userName = $this->Auth->user('username');

        $data = $this->request->getData();
        $sentenceLang = $data['selectedLang'];
        $sentenceText = $data['value'];
        $sentenceLicense = isset($data['sentenceLicense']) ?
                           $data['sentenceLicense'] : null;

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
            $this->set('duplicate', !$sentence->isNew());
            $this->set('sentence', $sentence);
        }

    }

    /**
     * Edit sentence. Used by AJAX request in sentences.edit_in_place.js.
     *
     * @return void
     */
    public function edit_sentence()
    {
        $sentence = $this->Sentences->editSentence($this->request->data);
        if (empty($sentence)) {
            $this->redirect(array('controller' => 'pages', 'action' => 'home'));
        } else {
            $this->layout = null;
            $this->set('sentence_text', $sentence->text);
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

        $this->Sentences->setOwner($id, $userId, CurrentUser::get('group_id'));

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
        $sentence = $this->Sentences->get($id, [
            'contain' => ['Users' => ['fields' => ['username']]],
            'fields' => ['id'],
        ]);

        $this->set('sentenceId', $id);
        $this->set('owner', $sentence->user);
        $this->layout = null;
        $this->render('adopt');
    }


    /**
     * Save the translation.
     *
     * @return void
     */
    public function save_translation()
    {
        $sentenceId = $_POST['id'];
        $translationLang = $_POST['selectLang'];
        $userId = $this->Auth->user('id');
        $userLevel = $this->Sentences->Users->getLevelOfUser($userId);

        if ($userLevel < 0) {
            return ;
        }

        $translationText = $_POST['value'];

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
            $criteriaVars[$name] = $default;
            if (isset($this->request->query[$name])) {
                $criteriaVars[$name] = $this->request->query[$name];
            }
        }
        extract($criteriaVars);
        $ignored = array();

        /* Convert simple search to advanced search parameters */
        if (isset($this->request->query['to'])
            && !isset($this->request->query['trans_to'])) {
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
            $result = $this->User->findByUsername($trans_user, 'id');
            if ($result) {
                $transFilter[] = 't.u='.$result['User']['id'];
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
            $result = $this->User->findByUsername($user, 'id');
            if ($result) {
                $user_id = $result['User']['id'];
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
            $tagsArray = explode(',', $tags);
            $tagsArray = array_map('trim', $tagsArray);
            $result = $this->Tag->find('all', array(
                'conditions' => array('name' => $tagsArray),
                'fields' => array('id', 'name')
            ));
            $tagsById = Set::combine($result, '{n}.Tag.id', '{n}.Tag.name');
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
                    if ($list == $rec['SentencesList']['id']) {
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
            $natives = $this->UsersLanguages->find('all', array(
                'conditions' => array(
                    'language_code' => $from,
                    'level' => 5,
                ),
                'fields' => array('of_user_id'),
            ));
            $natives = Set::extract($natives, '{n}.UsersLanguages.of_user_id');
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

        $sphinx_markers = $this->_find_sphinx_markers($query);
        if (!empty($sphinx_markers)) {
            $this->set(compact('sphinx_markers'));
        }
        
        $model = 'Sentences';
        if (CurrentUser::isMember()) {
            $contain = $this->Sentences->contain();
        } else {
            $contain = $this->Sentences->minimalContain();
        }
        $pagination = [
            'finder' => 'filteredTranslations',
            'fields' => $this->Sentences->fields(),
            'contain' => $contain,
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'sphinx' => $sphinx,
            'search' => $query
        ];

        $results = $this->_common_sentences_pagination(
            $pagination,
            $model,
            $to
        );
        $real_total = $this->Sentences->getRealTotal();
        $results = $this->Sentences->addHighlightMarkers($this->Sentences->getAlias(), $results, $query);

        $strippedQuery = preg_replace('/"|=/', '', $query);
        $this->loadModel('Vocabulary');
        $vocabulary = $this->Vocabulary->findByText($strippedQuery);

        $this->set('vocabulary', $vocabulary);
        $this->set('searchableLists', $searchableLists);
        $this->set(compact(array_keys($this->defaultSearchCriteria)));
        $this->set(compact('real_total', 'search_disabled', 'ignored', 'results'));
        $this->set(
            'is_advanced_search',
            isset($this->request->query['trans_to'])
        );
    }

    public function advanced_search() {
        $this->set('searchableLists', $this->SentencesList->getSearchableLists());
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
                'translationLang' => $translationLang
            ]],
            'fields' => $this->Sentences->fields(),
            'contain' => $this->Sentences->paginateContain($translationLang),
            'conditions' => $conditions,
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'order' => ['Sentences.id' => 'DESC']
        ];

        $this->paginate = $pagination;
        $allSentences = $this->paginate();

        $this->set('lang', $lang);
        $this->set('translationLang', $translationLang);
        $this->set('results', $allSentences);

        $this->Cookie->write('browse_sentences_in_lang', $lang, false, "+1 month");
        $this->Cookie->write('show_translations_into_lang', $translationLang, false, "+1 month");
        $this->render(null);
    }
    /**
     * Return all information needed to display a paginated
     * list of sentences
     *
     * @param array  $pagination      The pagination request.
     * @param string $model           Model to use for pagination
     * @param string $translationLang If different of null, will only
     *                                retrieve translation in this language.
     * @param int    &$real_total     If Sphinx returns the "real total", it
     *                                will be stored here. Sphinx returns a
     *                                limited number of results (1000), but
     *                                it's able to tell the exact number of
     *                                results that could be returned if there
     *                                were no limitation.
     *
     * @return array Big nested array of sentences + information related to senences
     */
    private function _common_sentences_pagination(
        $pagination,
        $model,
        $translationLang = null,
        &$real_total = 0
    ) {
        if ($translationLang == "none") {
            unset($pagination[$model]['contain']['Translation']);
        } elseif ($translationLang != "und") {
            $this->Sentence->linkTranslationModel(array(
                'Translation.lang' => $translationLang
            ));
        }

        $this->paginate = $pagination;
        $results = $this->paginate($model);

        return $results;
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
        $lang = Sanitize::paranoid($lang);

        if ($lang == null) {
            $lang = $this->request->getSession()->read('random_lang_selected');
        }

        $randomId = $this->Sentence->getRandomId($lang);

        if (is_null($randomId)) {
            $this->set('searchProblem', true);
            $randomSentence = null;
        } else {
            $randomSentence = $this->Sentence->getSentenceWithId($randomId);
        }

        $this->request->getSession()->write('random_lang_selected', $lang);
        $this->set('random', $randomSentence);
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
        $this->_sentences_of_user_common($userId, $lang);
        $this->set("lang", $lang);

    }

    /**
     * Private function to factorize my_sentences and of_user
     *
     * @param int    $userId The id of the user whose we want the sentences.
     * @param string $lang   Filter only the sentences in this language.
     *
     * @return void
     */
    private function _sentences_of_user_common($userId, $lang)
    {
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
                'limit' => 100,
                'order' => "Sentences.modified DESC"

            )
        );


        $conditions = $this->Sentences->find()->where(['user_id' => $userId]);
        // if the lang is specified then we also filter on the language
        if (!empty($lang)) {
            $conditions = $conditions->where(['lang' => $lang]);
        }

        $sentences = $this->paginate($conditions);

        $this->set('user_sentences', $sentences);
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
        $neighbors = $this->Sentence->getNeighborsSentenceIds($id, $lang);
        $this->set('nextSentence', $neighbors['next']);
        $this->set('prevSentence', $neighbors['prev']);
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
                    "action" => "home",
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
        $sentenceId = $this->request->data['Sentence']['id'];
        $correctness = $this->request->data['Sentence']['correctness'];

        if (CurrentUser::isModerator()) {
            $this->Sentence->editCorrectness($sentenceId, $correctness);
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
        $sentenceId = $this->request->data['Sentence']['id'];
        $ownerName = $this->request->data['Sentence']['ownerName'];
        $hasaudio = $this->request->data['Sentence']['hasaudio'];

        if (CurrentUser::isAdmin()) {
            if ($hasaudio) {
                $this->Audio->assignAudioTo($sentenceId, $ownerName);
            } else {
                $this->Audio->deleteAll(array('sentence_id' => $sentenceId), false, true);
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

        if (CurrentUser::isModerator()) {
            unset($this->Sentences->validate['license']['canSwitchLicense']);
        }

        $sentence = $this->Sentences->get($sentenceId);
        $sentence = $this->Sentences->patchEntity($sentence, ['license' => $newLicense]);

        if (!CurrentUser::isModerator()) {
            $this->Flash->set(__('You are not allowed to change the license of this sentence.'));
        }
        elseif ($this->Sentences->save($sentence)) {
            $this->Flash->set(format(
                __('The license of the sentence has been changed to “{newLicense}”.'),
                compact('newLicense')
            ));
        } else {
            if (isset($this->Sentences->validationErrors['license'])) {
                $message = format(
                    __('Unable to change the license to “{newLicense}” because:'),
                    compact('newLicense')
                );
                $errors = $this->Sentences->validationErrors['license'];
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
