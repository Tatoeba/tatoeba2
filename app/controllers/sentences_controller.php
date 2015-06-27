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
    public $persistentModel = true;
    public $name = 'Sentences';
    public $components = array (
        'LanguageDetection',
        'CommonSentence',
        'Permissions',
        'Cookie'
    );
    public $helpers = array(
        'Sentences',
        'Menu',
        'SentenceButtons',
        'Html',
        'Logs',
        'Pagination',
        'Comments',
        'Navigation',
        'Languages',
        'Javascript',
        'CommonModules'
    );
    public $paginate = array(
        'limit' => 100,
        "order" => "Sentence.modified DESC"
    );

    public $uses = array(
        'Sentence',
        'SentenceNotTranslatedInto',
        'SentencesSentencesLists',
        'User',
        'Tag',
    );

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array(
            'index',
            'show',
            'search',
            'of_user',
            'random',
            'go_to_sentence',
            'several_random_sentences',
            'get_neighbors_for_ajax',
            'show_all_in',
            'with_audio'
        );
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

        $id = Sanitize::paranoid($id);

        if ($id == "random" || $id == null || $id == "" ) {
            $id = $this->Session->read('random_lang_selected');
            $id = Sanitize::paranoid($id);
        }

        if (is_numeric($id)) {

            // ----- if we give directly an id -----
            // Whether the sentence still exists or not, we retrieve the
            // contributions and the comments because we don't want them
            // to disappear just because the sentence was deleted.
            $contributions = $this->Sentence->getContributionsRelatedToSentence(
                $id
            );

            $comments = $this->Sentence->getCommentsForSentence($id);
            $commentsPermissions = $this->Permissions->getCommentsOptions($comments);

            $this->set('sentenceComments', $comments);
            $this->set('commentsPermissions', $commentsPermissions);
            $this->set('contributions', $contributions);

            // And now we retrieve the sentence
            $sentence = $this->Sentence->getSentenceWithId($id);

            $canComment = CurrentUser::isMember()
                && (!empty($contributions) || !empty($sentence));
            $this->set('canComment', $canComment);

            // this way "next" and "previous"
            $lang = $this->Session->read('random_lang_selected');
            $neighbors = $this->Sentence->getNeighborsSentenceIds($id, $lang);
            $this->set('nextSentence', $neighbors['next']);
            $this->set('prevSentence', $neighbors['prev']);

            // If no sentence, we don't need to go further.
            // We just set some variable so we don't get warnings.
            if ($sentence == null) {
                $this->set('sentenceId', $id);
                $this->set('tagsArray', array());
                return;
            }

            $tagsArray = $this->Sentence->getAllTagsOnSentence($id);
            $listsArray = $this->SentencesSentencesLists->getListsForSentence($id);

            $this->set('sentence', $sentence);

            // we get translations and split them
            $alltranslations = $this->Sentence->getTranslationsOf($id);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            $this->set('tagsArray', $tagsArray);
            $this->set('listsArray', $listsArray);
            $this->set('translations', $translations);
            $this->set('indirectTranslations', $indirectTranslations);

        } else {
            // ----- if we want a random sentence in a specific language -----
            // here only to make things clearer as "id" is not a number
            if (array_key_exists($id, LanguagesLib::languagesInTatoeba())) {
                $lang = $id;
            } else {
                $lang = null;
            }

            $randomId = $this->Sentence->getRandomId($lang);

            $this->Session->write('random_lang_selected', $lang);
            $this->redirect(array("action"=>"show", $randomId));

        }
    }


    /**
     * Display sentence of specified id.
     *
     * @return void
     */

    public function go_to_sentence()
    {
        $id = intval($this->params['url']['sentence_id']);
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
    public function delete($id = null)
    {
        $id = Sanitize::paranoid($id);
        if (empty($id)) {
            return;
        }

        $this->Sentence->recursive = -1;
        $sentence = $this->Sentence->findById($id);
        if (!$sentence) {
            $this->redirect(array('controller' => 'pages', 'action' => 'home'));
            return;
        }

        if (!CurrentUser::canRemoveSentence($sentence['Sentence']['id'], $sentence['Sentence']['user_id'])) {
            $this->flash(
                __('You cannot delete this sentence.', true),
                '/sentences/show/'.$id
            );
            return;
        }

        $isDeleted = $this->Sentence->delete(
            $id,
            true
        );
        if ($isDeleted) {
            $this->flash(
                format(
                    __('The sentence #{id} has been deleted.', true),
                    array("id" => $id)
                ),
                '/sentences/show/'.$id
            );
        } else {
            $this->flash(
                format(
                    __('Error: the sentence #{id} could not be deleted.', true),
                    array("id" => $id)
                ),
                '/sentences/show/'.$id
            );
        }
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
        $userLevel = $this->Sentence->User->getLevelOfUser($userId);
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

        $sentenceLang = Sanitize::paranoid($_POST['selectedLang']);
        $sentenceText = $_POST['value'];

        $isSaved = $this->CommonSentence->wrapper_save_sentence(
            $sentenceLang,
            $sentenceText,
            $userId,
            null,
            $userName
        );

        // saving
        if ($isSaved) {
            $this->layout = null;

            $sentenceId = $this->Sentence->id;
            $sentence = $this->Sentence->getSentenceWithId($sentenceId);

            $this->set('sentence', $sentence);
        }

    }

    /**
     * Edit sentence.
     * Used in AJAX request, in sentences.edit_in_place.js.
     *
     * @todo Need to have an editSentence() in the model, that will check if sentence 
     * has audio, in which case it cannot be edited.
     *
     * @return void
     */
    public function edit_sentence()
    {
        $sentenceText = '';
        $sentenceId = '';
        if (isset($this->params['form']['value'])) {
            $sentenceText = trim($this->params['form']['value']);
        }
        if (isset($this->params['form']['id'])) {
            $sentenceId = $this->params['form']['id']; // NOTE: do not Sanitize::paranoid() this
                                                       // ...because hack mentionned below
        }

        if (!isset($sentenceText) || $sentenceText === '') {
            // if the sentence contain no text (empty or only space)
            // the we directly return without saving
            return;
        }

        if (isset($sentenceId)) {
            // TODO HACK SPOTTED $this->params['form']['id'] store 2 informations, lang and id
            // related to HACK in edit in place.js
            $hack_array = explode("_", $sentenceId);

            $realSentenceId = Sanitize::paranoid($hack_array[1]);
            $sentenceLang = Sanitize::paranoid($hack_array[0]);

            $this->Sentence->recursive = -1;
            $sentence = $this->Sentence->findById($realSentenceId);
            if (!$sentence || !CurrentUser::canEditSentenceOfUserId($sentence['Sentence']['user_id'])) {
                $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		return;
            }

            $this->Sentence->id = $realSentenceId;
            $data['Sentence']['lang'] = $sentenceLang;
            $data['Sentence']['text'] = $sentenceText;
            $isSaved = $this->Sentence->save($data);

            if ($isSaved) {
                $this->layout = null;
                $this->set('sentence_text', $sentenceText);
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
        $id = Sanitize::paranoid($id);
        $userId = $this->Auth->user('id');

        $this->Sentence->setOwner($id, $userId);

        $this->renderAdopt($id, $userId);
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
        $id = Sanitize::paranoid($id);
        $userId = $this->Auth->user('id');

        $this->Sentence->unsetOwner($id, $userId);

        $this->renderAdopt($id, $userId);
    }

    private function renderAdopt($id, $userId)
    {
        $sentence = $this->Sentence->find('first', array(
            'conditions' => array('Sentence.id' => $id),
            'contain' => array('User' => 'username'),
            'fields' => array('id'),
        ));

        $ownerName = $sentence['User'] ? $sentence['User']['username'] : null;
        $this->set('sentenceId', $id);
        $this->set('ownerName', $ownerName);
        $this->layout = null;
        $this->render('adopt');
    }

    private function _setSentenceData($id)
    {
        $sentence = $this->Sentence->getSentenceWithId($id);
        $allTranslations = $this->Sentence->getTranslationsOf($id);
        $translations = $allTranslations['Translation'];
        $indirectTranslations = $allTranslations['IndirectTranslation'];

        $this->set('sentence', $sentence);
        $this->set('translations', $translations);
        $this->set('indirectTranslations', $indirectTranslations);
    }

    /**
     * Save the translation.
     *
     * @return void
     */
    public function save_translation()
    {
        $sentenceId = Sanitize::paranoid($_POST['id']);
        $translationLang = Sanitize::paranoid($_POST['selectLang']);
        $userId = $this->Auth->user('id');
        $userLevel = $this->Sentence->User->getLevelOfUser($userId);

        if ($userLevel < 0) {
            return ;
        }

        $translationText = $_POST['value'];
        
        // we store the selected language to be reused
        // since users are likely to contribute in the 
        // same language; they don't need to reselect each time
        $this->Cookie->write('contribute_lang', $translationLang, false, "+1 month");

        if (isset($translationText)
            && trim($translationText) != ''
            && isset($sentenceId)
            && !(empty($userId))
        ) {
            // Language detection
            if ($translationLang == 'auto') {

                $ownerName = $this->Auth->user('username');
                $translationLang = $this->LanguageDetection->detectLang(
                    $translationText,
                    $ownerName
                );
            }

            // Saving...
            $sentenceLang = $this->Sentence->getLanguageCodeFromSentenceId($sentenceId);
            $isSaved = $this->Sentence->saveTranslation(
                $sentenceId,
                $sentenceLang,
                $translationText,
                $translationLang
            );

            if ($isSaved) {
                // We reconstruct the translation to use it in the helper's function
                $translation['id'] = $this->Sentence->id;
                $translation['lang'] = $translationLang;
                $translation['text'] = $translationText;
                $translation['correctness'] = 0;

                $this->set('translation', $translation);
                $this->set('parentId', $sentenceId);
            }
        }
    }

    /**
     * Search sentences.
     *
     * @param string $query The research query.
     *
     * @return void
     */
    public function search()
    {
        $criteria = array(
            'query' => '',
            'from' => 'und',
            'to' => 'und',
            'tags' => '',
            'user' => '',
            'orphans' => 'no',
            'unapproved' => 'no',
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
        $criteriaVars = array();
        foreach ($criteria as $name => $default) {
            $criteriaVars[$name] = $default;
            if (isset($this->params['url'][$name])) {
                $criteriaVars[$name] = $this->params['url'][$name];
            }
        }
        extract($criteriaVars);

        /* Convert simple search to advanced search parameters */
        if (isset($this->params['url']['to'])
            && !isset($this->params['url']['trans_to'])) {
            $trans_to = $to;
        }

        // Session variables for search bar
        $this->Session->write('search_query', $query);
        $this->Session->write('search_from', $from);
        $this->Session->write('search_to', $to);

        // replace strange space
        $query = str_replace(
            array('　', ' '),
            ' ',
            $query
        );

        $ranking_formula = 'text_len';
        $sortMode = '@rank';
        if ($sort == 'random') {
            $sortMode = '@random';
        } elseif ($sort == 'created') {
            $ranking_formula = '-created';
        }
        $sortMode .= empty($sort_reverse) ? ' ASC' : ' DESC';
        $index = $from == 'und' ?
                 array('und_index') :
                 array($from . '_main_index', $from . '_delta_index');
        $sphinx = array(
            'index' => $index,
            'matchMode' => SPH_MATCH_EXTENDED2,
            'sortMode' => array(SPH_SORT_EXTENDED => $sortMode),
            'rankingMode' => array(SPH_RANK_EXPR => $ranking_formula),
        );
        if (empty($query)) {
            // When the query is empty, Sphinx changes matchMode into
            // SPH_MATCH_FULLSCAN and ignores rankingMode. So let's use
            // sortMode instead.
            if (!empty($sort_reverse)) {
                $ranking_formula = "-($ranking_formula)";
            }
            $sphinx['sortMode'] = array(SPH_SORT_EXPR => $ranking_formula);
        }

        $transFilter = array();
        // if we want to search only on sentences having translations
        // in a specified language
        if ($trans_to !== 'und') {
            $this->loadModel('Language');
            $toId = $this->Language->getIdFromLang($trans_to);
            if ($toId) {
                $transFilter[] = "t.l=$toId";
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
                $trans_orphan = 'no';
            } else {
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
        if (!empty($user)) {
            $result = $this->User->findByUsername($user, 'id');
            if ($result) {
                $sphinx['filter'][] = array('user_id', $result['User']['id']);
                if ($orphans == 'yes') {
                    $orphans = '';
                }
            } else {
                $user = '';
            }
        }

        // filter by tags
        if (!empty($tags)) {
            $tagsArray = explode(',', $tags);
            $tagsArray = array_map('trim', $tagsArray);
            $result = $this->Tag->find('all', array(
                'conditions' => array('name' => $tagsArray),
                'contain' => array(),
                'fields' => array('id', 'name')
            ));
            $tagsById = Set::combine($result, '{n}.Tag.id', '{n}.Tag.name');
            if ($tagsById) {
                foreach (array_keys($tagsById) as $id)
                    $sphinx['filter'][] = array('tags_id', $id);
            }

            // clean provided list
            foreach ($tagsArray as $i => $name) {
                if (!in_array($name, $tagsById))
                    unset($tagsArray[$i]);
            }
            $tags = implode(',', $tagsArray);
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

        // filter audio
        if (!empty($has_audio)) {
            $audio = $has_audio == 'yes' ? 1 : 0;
            $sphinx['filter'][] = array('has_audio', $audio);
        }

        $model = 'Sentence';
        $pagination = array(
            'Sentence' => array(
                'fields' => array(
                    'id',
                ),
                'contain' => array(),
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'sphinx' => $sphinx,
                'search' => $query
            )
        );

        $allSentences = $this->_common_sentences_pagination(
            $pagination,
            $model,
            $to,
            $real_total
        );

        $this->set(compact(array_keys($criteria)));
        $this->set('results', $allSentences);
        $this->set('real_total', $real_total);
        $this->set(
            'is_advanced_search',
            isset($this->params['url']['trans_to'])
        );
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
    public function show_all_in(
        $lang,
        $translationLang,
        $notTranslatedInto,
        $filterAudioOnly = "indifferent"
    ) {
        // TODO This is a hack. We need to find out how to make
        // a form in contribute.ctp that directly forges a CakePHP-compliant URL
        if (isset($_POST['data']['Sentence']['into'])) {
            $this->redirect(
                array(
                    "controller" => "sentences",
                    "action" => "show_all_in",
                    $_POST['data']['Sentence']['into'],
                    'none',
                    'none',
                    'indifferent'
                )
            );
        }

        $this->helpers[] = 'ShowAll';

        $model = 'Sentence';

        if ($lang == 'unknown') {
            $lang = null;
        }

        $pagination = array(
            'Sentence' => array(
                'fields' => array(
                    'id',
                ),
                'conditions' => array(
                    'lang' => $lang,
                ),
                'contain' => array(),
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'order' => "Sentence.id desc"
            )
        );

        // filter or not sentences-with-audio-only
        $audioOnly = false ;
        if ($filterAudioOnly === "only-with-audio") {
            $audioOnly = true ;
            $pagination['Sentence']['conditions']['hasaudio !='] = "no";
        }


        if (!empty($notTranslatedInto) && $notTranslatedInto != 'none') {

            $model = 'SentenceNotTranslatedInto';
            $pagination = array(
                'SentenceNotTranslatedInto' => array(
                    'fields' => array(
                        'id',
                    ),
                    'conditions' => array(
                        'source' => $lang,
                        'translatedInto' => $translationLang,
                        'notTranslatedInto' => $notTranslatedInto,
                        'audioOnly' => $audioOnly,
                    ),
                    'contain' => array(),
                    'limit' => 10,
                )
            );
        }

        $allSentences = $this->_common_sentences_pagination(
            $pagination,
            $model,
            $translationLang
        );

        if ($lang === null) {
            $lang = 'unknown';
        }

        $this->set('lang', $lang);
        $this->set('translationLang', $translationLang);
        $this->set('notTranslatedInto', $notTranslatedInto);
        $this->set('filterAudioOnly', $filterAudioOnly);
        $this->set('results', $allSentences);

        $this->Cookie->write('browse_sentences_in_lang', $lang, false, "+1 month");
        $this->Cookie->write('show_translations_into_lang', $translationLang, false, "+1 month");
        $this->Cookie->write('not_translated_into_lang', $notTranslatedInto, false, "+1 month");
        $this->Cookie->write('filter_audio_only', $filterAudioOnly, false, "+1 month");
    }
    /**
     * Return all information needed to display a paginated
     * list of sentences
     *
     * @param array  $pagination      The pagination request.
     * @param string $model           Model to use for pagination
     * @param string $translationLang If different of null, will only
     *                                retrieve translation in this language.
     * @param string &$real_total     If Sphinx returns the "real total", it
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

        $this->paginate = $pagination;
        $results = $this->paginate($model);
        if (!is_array($results)) {
            $results = array();
        }
        if (isset($results[0]['Sentence']['_total_found'])) {
            $real_total = $results[0]['Sentence']['_total_found'];
        }

        $sentenceIds = array();

        foreach ($results as $i=>$sentence) {
            $sentenceIds[$i] = $sentence['Sentence']['id'];
        }


        if ($translationLang == "und") {
            $translationLang = null ;
        }

        $allSentences = $this->CommonSentence->getAllNeededForSentences(
            $sentenceIds,
            $translationLang
        );
        return $allSentences;
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
            $lang = $this->Session->read('random_lang_selected');
        }

        $randomId = $this->Sentence->getRandomId($lang);
        $randomSentence = $this->Sentence->getSentenceWithId($randomId);
        $alltranslations = $this->Sentence->getTranslationsOf($randomId);
        $translations = $alltranslations['Translation'];
        $indirectTranslations = $alltranslations['IndirectTranslation'];

        $this->Session->write('random_lang_selected', $lang);

        $this->set('random', $randomSentence);
        $this->set('translations', $translations);
        $this->set('indirectTranslations', $indirectTranslations);
    }

    /**
     * show several random sentences a time
     * NOTE : TODO : this just a work around !
     *
     * @return void
     */
    public function several_random_sentences()
    {

        if (isset($_POST['data']['Sentence']['numberWanted'])) {
            $number = $_POST['data']['Sentence']['numberWanted'];
            $number = Sanitize::paranoid($number);
        } else {
            // default number of sentences when coming from "show more..."
            $number = 5;
        }

        if (isset($_POST['data']['Sentence']['into'])) {
            $lang = $_POST['data']['Sentence']['into'] ;
            $lang = Sanitize::paranoid($lang);
            $this->Session->write('random_lang_selected', $lang);
        } else {
            // default language when coming from "show more..."
            $lang = $this->Session->read('random_lang_selected');
        }

        $type = null ;
        // to avoid "petit malin"
        if ( $number > 15 or $number < 1) {
            $number = 5 ;
        }

        // for far better perfomance we must do it in one request, but hmmm no time
        // for that and as said above that's a work around

        $randomIds = $this->Sentence->getSeveralRandomIds($lang, $number);

        $this->Session->write('random_lang_selected', $lang);

        $allSentences = $this->CommonSentence->getAllNeededForSentences($randomIds);

        $this->set("allSentences", $allSentences);
        $this->set('lastNumberChosen', $number);
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
        $lang = Sanitize::paranoid($lang);

        $this->loadModel('User');
        $userId = $this->User->getIdFromUserName($userName);

        $backLink = $this->referer(array('action'=>'index'), true);
        // if there's no such user no need to do more computation

        $this->set('backLink', $backLink);
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
            'Sentence' => array(
                'fields' => array(
                    'id',
                    'text',
                    'lang',
                    'user_id',
                    'correctness'
                ),
                'contain' => array(
                    'User' => array(
                        'fields' => array('username')
                    )
                ),
                'limit' => 100,
                'order' => "Sentence.modified DESC"

            )
        );


        $conditions = array(
            'Sentence.user_id' => $userId,
        );
        // if the lang is specified then we also filter on the language
        if (!empty($lang)) {
            $conditions = array(
                "AND" => array(
                    'Sentence.user_id' => $userId,
                    'Sentence.lang' => $lang
                )
            );
        }

        $sentences = $this->paginate(
            'Sentence',
            $conditions
        );

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
        if (isset($this->params['form']['id'])
            && isset($this->params['form']['newLang'])
        ) {
            $newLang = Sanitize::paranoid($this->params['form']['newLang']);
            $id = Sanitize::paranoid($this->params['form']['id']);

            $lang = $this->Sentence->changeLanguage($id, $newLang);
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
        $this->Session->write('random_lang_selected', $lang);
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
        $this->paginate = array(
            'Sentence' => array(
                'fields' => array(
                    'id',
                    'text',
                    'lang',
                    'hasaudio',
                    'correctness'
                ),
                'contain' => array(),
                'limit' => 50,
                'conditions' => array(
                    'hasaudio' => array('shtooka', 'from_users')
                )
            )
        );

        if ($lang != null) {
            $this->paginate['Sentence']['conditions']['lang'] = $lang;
        }

        $results = $this->paginate();

        $stats = $this->Sentence->getTotalNumberOfSentencesWithAudio();

        $this->set('results', $results);
        $this->set('lang', $lang);
        $this->set('stats', $stats);
    }
    
    /**
     * Edit correctness of a sentence.
     *
     * @return void
     */
    public function edit_correctness()
    {
        $sentenceId = $this->data['Sentence']['id'];
        $correctness = $this->data['Sentence']['correctness'];
        
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
        $sentenceId = $this->data['Sentence']['id'];
        $hasaudio = $this->data['Sentence']['hasaudio'];
        
        if (CurrentUser::isAdmin()) {
            $this->Sentence->editAudio($sentenceId, $hasaudio);
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
}
?>
