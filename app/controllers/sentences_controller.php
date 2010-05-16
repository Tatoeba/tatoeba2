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
    public $name = 'Sentences';
    public $components = array (
        'GoogleLanguageApi',
        'SaveSentence',
        'Lucene',
        'Permissions'
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
        'CommonModules',
    );
    public $paginate = array(
        'limit' => 100,
        "order" => "Sentence.modified DESC"
    );
    public $uses = array(
        'Sentence',
        'Translation',
        'Contribution',
        'SentenceComment'
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
            'add_comment',
            'random',
            'go_to_sentence',
            'statistics',
            'count_unknown_language',
            'get_translations',
            'change_language',
            'several_random_sentences',
            'sentences_group'
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
        $id = Sanitize::paranoid($id);
        
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');

        if ($id == "random" || $id == null || $id == "" ) {
            $id = $this->Session->read('random_lang_selected');
        }
        
        if (in_array($id, $this->Sentence->languages)) {
            // ----- if we want a random sentence in a specific language -----
            // here only to make things clearer  
            $lang = $id; 
            $randomId = $this->Sentence->getRandomId($lang);
            
            $this->Session->write('random_lang_selected', $lang);
            $this->redirect(array("action"=>"show", $randomId));  
        
        } elseif (is_numeric($id)) {
            // ----- if we give directly an id -----
            
            $sentence = $this->Sentence->getSentenceWithId($id);

            if ($sentence == null) {
                // if there's no sentence for this id, no need to call the other
                // methods, we return directly
                $this->set('sentenceExists', false);
                return;
            }

            $contributions = $this->Contribution->getContributionsRelatedToSentence(
                $id
            );
            $comments = $this->SentenceComment->getCommentsForSentence($id);
            $commentsPermissions = $this->Permissions->getCommentsOptions(
                $comments,
                $userId,
                $groupId 
            );
            $alltranslations = $this->Sentence->getTranslationsOf($id);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];
            
            $this->set('sentenceExists', true);
            $this->set('translations', $translations);
            $this->set('sentence', $sentence);
            $this->set('indirectTranslations', $indirectTranslations);
            $this->set('sentenceComments', $comments);
            $this->set('commentsPermissions', $commentsPermissions);
            $this->set('contributions', $contributions); 
            
        } else {
            // ----- other case -----
            $max = $this->Sentence->getMaxId();
            $randId = rand(1, $max);
            $this->Session->write('random_lang_selected', 'any');
            $this->redirect(
                array(
                    "action"=>"show",
                    $randId
                )
            );
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
        $userId = $this->Auth->user('id');
        $sentenceLang = $this->data['Sentence']['contributionLang'];
        $sentenceLang = Sanitize::paranoid($sentenceLang);
        $sentenceText = $this->data['Sentence']['text'];
        
        Sanitize::html($sentenceText);
        $sentenceText = trim($sentenceText);

        $this->Session->write('contribute_lang', $sentenceLang);

        if (empty($sentenceText) || empty($userId)) {
            return ;
        }

        // saving
        $isSaved = $this->SaveSentence->wrapper_save_sentence(
            $sentenceLang,
            $sentenceText,
            $userId
        );

        if ($isSaved) {
            $sentence = $this->Sentence->getSentenceWithId($this->Sentence->id);
            $this->set('sentence', $sentence);
        }
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
        $id = Sanitize::paranoid($id);
        $this->Sentence->delete(
            $id,
            true
        );
        $this->flash(
            'The sentence #'.$id.' has been deleted.', '/sentences/show/'.$id
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

        if (!isset($_POST['value'])
            || !isset($_POST['selectedLang'])
        ) {
            //TODO add error handling
            return;
        }
        $userId = $this->Auth->user('id');

        $sentenceLang = Sanitize::paranoid($_POST['selectedLang']);
        $sentenceText = $_POST['value'];

        $isSaved = $this->SaveSentence->wrapper_save_sentence(
            $sentenceLang,
            $sentenceText,
            $userId
        );
        
        $this->Session->write('contribute_lang', $sentenceLang);
        
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
     * @return void
     */
    public function edit_sentence()
    {
        $userId = $this->Auth->user('id');
        $sentenceText = '';
        $sentenceId = '';
        if (isset($_POST['value'])) {
            $sentenceText = trim($_POST['value']);
        }
        if (isset($_POST['id'])) {
            $sentenceId = $_POST['id'];
        }
        
        if (!isset($sentenceText) || $sentenceText === '') {
            // if the sentence contain no text (empty or only space)
            // the we directly return without saving
            return;
        }
        
        if (isset($sentenceId)) {
        
            Sanitize::html($sentenceText);
            $sentenceId = Sanitize::paranoid($sentenceId);
            
            // TODO HACK SPOTTED $_POST['id'] store 2 informations, lang and id
            // related to HACK in edit in place.js 
            $hack_array = explode("_", $sentenceId);
            $this->Sentence->id = $hack_array[1];
            $data['Sentence']['lang'] = $hack_array[0];
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
        //Configure::write("debug",0);
        $id = Sanitize::paranoid($id);
        $this->Sentence->id = $id;
        $userId = $this->Auth->user('id');
        $this->Sentence->saveField('user_id', $userId);
        
        $this->show($id);
        $this->render('sentences_group');
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
        $this->Sentence->id = $id;
        $this->Sentence->saveField('user_id', null);
        $this->show($id);
        $this->render('sentences_group');
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
        $withAudio = Sanitize::paranoid($_POST['withAudio']);
        $parentOwnerName = Sanitize::paranoid($_POST['parentOwnerName']);
        
        $userId = $this->Auth->user('id');
        $translationText = $_POST['value'];
        
        // we store the selected language to be reuse after
        // that way, as users are likely to contribute in the 
        // same language, they don't need to reselect each time
        $this->Session->write('contribute_lang', $translationLang);
        
        if (isset($translationText)
            && trim($translationText) != '' 
            && isset($sentenceId)
            && !(empty($userId))
        ) {
            // Language detection
            if ($translationLang == 'auto') {
                $translationLang = $this->GoogleLanguageApi->detectLang(
                    $translationText
                );
            }
            
            // Saving...
            $isSaved = $this->Sentence->saveTranslation(
                $sentenceId,
                $translationText,
                $translationLang
            );
            
            if ($isSaved) {
                // We reconstruct the translation to use it in the helper's function
                $translation['id'] = $this->Sentence->id;
                $translation['lang'] = $translationLang;
                $translation['text'] = $translationText;
                
                $ownerName = $this->Auth->user('username');
                
                $this->set('translation', $translation);
                $this->set('ownerName', $ownerName);
                $this->set('parentId', $sentenceId);
                $this->set('parentOwnerName', $parentOwnerName);
                $this->set('withAudio', $withAudio);
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
    public function search($query = null)
    {
        if (!isset($_GET['query'])) {
            $this->redirect(
                array(
                    "lang" => $this->params['lang'], 
                    "controller" => "pages", 
                    "action" => "display", 
                    "search"
                )
            );
        }
        
        
        $query = $_GET['query'];    
       
        $from = 'und'; 
        if (isset($_GET['from'])) {
            $from = $_GET['from'];
            $from = Sanitize::paranoid($from);
        }
       
        $to = 'und'; 
        if (isset($_GET['to'])) {
            $to = $_GET['to'];
            $to = Sanitize::paranoid($to);
        }
        
        // Session variables for search bar
        $this->Session->write('search_query', $query);
        $this->Session->write('search_from', $from);
        $this->Session->write('search_to', $to);
       
        $sphinx = array(
            'index' => array($from . "_" . $to . '_index'),
            'matchMode' => SPH_MATCH_EXTENDED2, 
            'sortMode' => array(SPH_SORT_RELEVANCE => "")
        );
        
        $pagination = array(
            'Sentence' => array(
                'fields' => array(
                    'id',
                ),
                'contain' => array(),
                'limit' => 10,
                'sphinx' => $sphinx,
                'search' => $query
            )
        );


        $this->paginate = $pagination;
        $results = $this->paginate();

        $sentenceIds = array();

        foreach($results as $i=>$sentence) {
            $sentenceIds[$i] = $sentence['Sentence']['id'];
        }

        $allSentences = $this->_getAllNeededForSentences($sentenceIds, $to);
        
        $this->set('query', $query);
        $this->set('results', $allSentences);
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
        $this->set('sentenceScript', $randomSentence['Sentence']['script']);
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
        
        $allSentences = $this->_getAllNeededForSentences($randomIds);
        
        $this->set("allSentences", $allSentences);
        $this->set('lastNumberChosen', $number);
    }
       
    private function _getAllNeededForSentences($sentenceIds, $lang = null)
    {
 
        $allSentences = array();
        
        foreach ($sentenceIds as $i=>$sentenceId) {

            $sentence = $this->Sentence->getSentenceWithId($sentenceId);
            

            $alltranslations = $this->Sentence->getTranslationsOf(
                $sentenceId,
                $lang
            );
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            $allSentences[$i] = array (
                "Sentence" => $sentence['Sentence'],
                "User" => $sentence['User'],
                "Translations" => $translations,
                "IndirectTranslations" => $indirectTranslations
            );
        }
        return $allSentences;
    }

    /**
     * Count number of sentences in each language.
     * TODO : should be move in the model
     *
     * @return array
     */
    public function statistics()
    {
        $stats = $this->Sentence->getStatistics();
        return($stats);
    }
    
    /**
     * Count number of sentences that belongs to the current user
     * and have an unidentified language.
     * Called in requestAction in pages/home.ctp.
     *
     * @return array
     */
    public function count_unknown_language()
    {
        return $this->Sentence->numberOfUnknownLanguageForUser(
            $this->Auth->user('id')
        );
    }
    
    /**
     * Display sentences with unknown language to let user
     * set the language.
     *
     * @return void
     */
    public function unknown_language()
    {
        $sentences = $this->Sentence->sentencesWithUnknownLanguageForUser(
            $this->Auth->user('id')
        );
        $this->set('unknownLangSentences', $sentences);
    }
    
    /**
     * Save languages for unknown language page.
     *
     * @return void
     */
    public function set_languages()
    {

        if (!empty($this->data)) {

            $sentences = $this->data['Sentence'];

            // if the language is still unknow
            // we unset the lang field to save it as NULL
            foreach ($sentences as $i=>$sentence) {
                if ($sentence['lang'] === 'xxx') {
                    unset($sentences[$i]['lang']);
                }
            }

            // TODO don't forget to update language stats count 

            if ($this->Sentence->saveAll($sentences)) {
                $flashMsg = __('The languages have been saved.', true);
            } else {
                $flashMsg = __('A problem occured while trying to save.', true);
            }
        } else {
            $flashMsg = __('There is nothing to save.', true);
        }
        
        $this->flash(
            $flashMsg,
            '/sentences/unknown_language/'
        );
        
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
        $UserModel = ClassRegistry::init('User');
        $userName = Sanitize::paranoid($userName);
        $lang = Sanitize::paranoid($lang);
        
        
        $userId = $UserModel->getIdFromUserName($userName);
        
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
        if (isset($lang)) {
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
     * Display how the sentences are clustered according to their language.
     *
     * @param int $page Page of the map.
     *
     * @return void
     */
    public function map($page = 1)
    {
        $page = Sanitize::paranoid($page);

        $total = 10000;
        $start = ($page-1) * $total;
        $end = $start + $total;
        
        $sentences = $this->Sentence->getSentencesForMap($start, $end);
        $this->set('page', $page);
        $this->set('all_sentences', $sentences);
    }
    
    /**
     * Change language of a sentence.
     * Used in AJAX request in sentences.change_language.js.
     * TODO restrict permissions for this action.
     *
     * @return void
     */
    public function change_language()
    {
        if (isset($_POST['id'])
            && isset($_POST['newLang'])
            && isset($_POST['prevLang'])
        ) {
            $newlang = $_POST['newLang'];
            $prevLang = $_POST['prevLang'];
            $id = $_POST['id'];
            $id = Sanitize::paranoid($id);
            $newLang = Sanitize::paranoid($newlang);
            $prevLang = Sanitize::paranoid($prevlang);

            // TODO create  method in the model to encapsulate this
            $this->Sentence->id = $id;
            $this->Sentence->saveField('lang', $newlang);

            $this->Contribution->updateLanguage($id, $newlang);

            $this->Sentence->incrementStatistics($newlang);
            $this->Sentence->decrementStatistics($prevLang);
        }
    }
    
}
?>
