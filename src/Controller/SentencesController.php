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
use App\Form\SentencesSearchForm;
use App\Model\CurrentUser;
use App\Model\Table\SentencesTable;
use App\Lib\LanguagesLib;
use App\Lib\SphinxClient;
use App\Lib\Licenses;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Routing\Router;
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
        'Languages',
        'CommonModules'
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

    public function initialize() {
        parent::initialize();

        $noCsrfActions = ['edit_sentence', 'change_language'];
        if (in_array($this->request->getParam('action'), $noCsrfActions)) {
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

    public function index()
    {
        $this->loadModel('Languages');
        $milestones = [ 100000, 10000, 1000, 100, 10, 1, 0 ];
        $stats = $this->Languages->getMilestonedStatistics($milestones);
        $nbrLanguages = count(LanguagesLib::languagesInTatoeba());
        $this->set(compact('stats', 'nbrLanguages'));
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
        $this->helpers[] = 'ClickableLinks';

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
            $sentence = $this->Sentences->getSentenceWith($id, [
                'translations' => true,
                'sentenceDetails' => true
            ]);

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
        // Users without a profile language should not be able to add sentences
        if (empty(CurrentUser::getProfileLanguages())) {
            return;
        }

        $userId = $this->Auth->user('id');
        $userLevel = $this->Sentences->Users->getLevelOfUser($userId);
        if ($userLevel < 0) {
            return;
        }

        $sentenceLang = $this->request->getData('selectedLang');
        $sentenceText = $this->request->getData('value');

        if (empty($sentenceText) || empty($sentenceLang)) {
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
            $sentence = $this->Sentences->getSentenceWith(
                $savedSentence->id,
                ['translations' => true] // in case it's a duplicate
            );
            $this->set('duplicate', $savedSentence->isDuplicate);
            $this->set('sentence', $sentence);
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->loadComponent('RequestHandler');
            $this->set('_serialize', ['sentence', 'duplicate']);
            $this->RequestHandler->renderAs($this, 'sentences_json');
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
            $sentence = $this->Sentences->getSentenceWith($sentence->id);
            $this->loadComponent('RequestHandler');
            $this->set('result', $sentence);
            $this->set('_serialize', ['result']);
            $this->RequestHandler->renderAs($this, 'json');
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

        if ($acceptsJson) {
            $sentence = $this->Sentences->getSentenceWith($id);
            $this->loadComponent('RequestHandler');
            $this->set('sentence', $sentence);
            $this->set('_serialize', ['sentence']);
            $this->RequestHandler->renderAs($this, 'json');
        } else {
            $sentence = $this->Sentences->get($id, [
                'contain' => ['Users' => ['fields' => ['username']]]
            ]);
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
                $translation->isDirect = true;
                $this->set('translation', $translation);
                $this->set('parentId', $sentenceId);
            }
        }

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $translationLangFilter = $this->request->getQuery('translationLang');
            $numberOfTranslations = $this->request->getQuery('numberOfTranslations');
            $includeTranslations = $translationLangFilter == 'und';
            $sentence = $this->Sentences->getSentenceWith($sentenceId, ['translations' => $includeTranslations]);
            $sentence->extraTranslationsCount = $numberOfTranslations + 1 - $sentence->max_visible_translations;

            $this->loadComponent('RequestHandler');
            $this->set('sentence', $sentence);
            $this->set('_serialize', ['translation', 'sentence']);
            $this->RequestHandler->renderAs($this, 'sentences_json');
        }
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

        /* Apply search criteria and sort */
        $search = new SentencesSearchForm();
        $search->setData($this->request->getQueryParams());

        /* Control input */
        if ($search->generateRandomSeedIfNeeded()) {
            return $this->redirect(Router::url($search->getData()));
        }
        $search->checkUnwantedCombinations();

        /* Session variables for search bar */
        $this->request->getSession()->write('search_from', $search->getData('from'));
        $this->request->getSession()->write('search_to', $search->getData('to'));
        $this->addLastUsedLang($search->getData('from'));
        $this->addLastUsedLang($search->getData('to'));

        $limit = CurrentUser::getSetting('sentences_per_page');
        $sphinx = $search->asSphinx();
        $sphinx['page'] = $this->request->query('page');
        $sphinx['limit'] = $limit;

        $model = 'Sentences';
        $query = $this->Sentences
            ->find('hideFields')
            ->find('withSphinx')
            ->find('nativeMarker')
            ->find('filteredTranslations', [
                'translationLang' => $search->getData('to'),
            ])
            ->select($this->Sentences->fields())
            ->contain($this->Sentences->contain(['translations' => true]));

        $this->paginate = [
            'limit' => $limit,
            'sphinx' => $sphinx,
        ];

        try {
            $results = $this->paginate($query);
            $real_total = $this->Sentences->getRealTotal();
            $results = $this->Sentences->addHighlightMarkers($results);
            $this->set(compact('results', 'real_total'));
        } catch (Exception $e) {
            $syntax_error = strpos($e->getMessage(), 'syntax error,') !== FALSE;
            if ($syntax_error) {
                $this->set('syntax_error', true);
            } else {
                $this->loadComponent('Error');
                $error_code = $this->Error->traceError('Search error: ' . $e->getMessage());
                $this->set('error_code', $error_code);
            }
        }

        $strippedQuery = preg_replace('/"|=/', '', $search->getData('query'));
        $this->loadModel('Vocabulary');
        $vocabulary = $this->Vocabulary->findByText($strippedQuery);

        $searchableLists = $search->getSearchableLists(CurrentUser::get('id'));
        $ignored = $search->getIgnoredFields();

        $this->set($search->getData());
        $this->set(compact('ignored', 'searchableLists', 'vocabulary'));
        $this->set(
            'is_advanced_search',
            !is_null($this->request->getQuery('trans_to'))
        );
    }

    public function advanced_search() {
        $search = new SentencesSearchForm();

        $search->setData($this->request->getQueryParams());
        $usesTemplate = !$search->isUsingDefaultCriteria();

        $this->set($search->getData() + compact('usesTemplate'));

        $searchableLists = $search->getSearchableLists(CurrentUser::get('id'));
        $this->set(compact('searchableLists'));
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

        $query = $this->Sentences->find();
        if ($lang == 'unknown') {
            $query->where(['Sentences.lang IS' => null]);
        } else {
            $query->where(['Sentences.lang' => $lang]);
        }
        $total = $query->count();

        $query->find('filteredTranslations', [
                  'translationLang' => $translationLang,
              ])
              ->find('nativeMarker')
              ->find('hideFields')
              ->select($this->Sentences->fields())
              ->contain($this->Sentences->contain(['translations' => true]))
              ->order(['Sentences.id' => 'DESC']);

        $this->addLastUsedLang($lang);
        $this->addLastUsedLang($translationLang);

        $this->paginate = [
            'limit' => CurrentUser::getSetting('sentences_per_page'),
        ];
        $totalLimit = $this::PAGINATION_DEFAULT_TOTAL_LIMIT;
        $query->find('latest', ['maxResults' => $totalLimit]);
        $allSentences = $this->paginateOrRedirect($query);

        $this->set('lang', $lang);
        $this->set('translationLang', $translationLang);
        $this->set('results', $allSentences);
        $this->set('total', $total);
        $this->set('totalLimit', $totalLimit);

        $this->Cookie->write('browse_sentences_in_lang', $lang, false, "+1 month");
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
        $randomId = $this->Sentences->getRandomId($lang);

        if (is_null($randomId)) {
            $this->set('searchProblem', true);
            $randomSentence = null;
        } else {
            $randomSentence = $this->Sentences->getSentenceWith(
                $randomId,
                ['translations' => true]
            );
        }

        $this->request->getSession()->write('random_lang_selected', $lang);
        $this->set('random', $randomSentence);

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->loadComponent('RequestHandler');
            $this->set('sentence', $randomSentence);
            $this->set('_serialize', ['sentence']);
            $this->RequestHandler->renderAs($this, 'sentences_json');
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

        $user = $this->Users->getUserById($userId);
        $this->set("unreliableButton", CurrentUser::canMarkSentencesOfUser($user));

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
                'order' => ['modified' => 'DESC']
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


    /**
     * Mark all sentences of a user as incorrect.
     *
     * @param string $username User name of the user.
     *
     * @return void
     */
    public function mark_unreliable($username)
    {
        $marked = $this->Sentences->markUnreliable($username);

        if($marked === false) {
            $message = __d(
                'admin',
                'Error: Sentences added by {username} could not be marked as unreliable.'
            );
        } else {
            $message = __d(
                'admin',
                'Marked all sentences added by {username} as unreliable.'
            );
        }

        $this->Flash->set(format($message, ['username' => $username]));
        $this->redirect(["controller" => "sentences", "action" => "of_user", $username]);
    }

    public function edit_license()
    {
        $sentenceId = $this->request->getData('id');
        $newLicense = $this->request->getData('license');
        if (is_null($sentenceId) || is_null($newLicense)) {
            throw new \Cake\Http\Exception\BadRequestException();
        }

        $sentence = $this->Sentences->get($sentenceId);

        if (!CurrentUser::canEditLicenseOfSentence($sentence)) {
            $this->Flash->set(__('You are not allowed to change the license of this sentence.'));
        } else {
            $sentence = $this->Sentences->patchEntity($sentence, ['license' => $newLicense]);
            $errors = $sentence->getError('license');
            $licenseName = Licenses::getSentenceLicenses()[$newLicense]['name'] ?? $newLicense;
            if (empty($errors) && $this->Sentences->save($sentence)) {
                $this->Flash->set(format(
                    __('The license of the sentence has been changed to “{newLicense}”.'),
                    ['newLicense' => $licenseName]
                ));
            } elseif (!empty($errors)) {
                $message = format(
                    __('Unable to change the license to “{newLicense}” because:'),
                    ['newLicense' => $licenseName]
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
