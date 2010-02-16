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
    public $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
    public $helpers = array(
        'Sentences', 'Html', 'Logs', 'Pagination', 'Comments', 'Navigation',
        'Languages', 'Javascript'
    );
    public $paginate = array('limit' => 100, "order" => "Sentence.modified DESC");
    public $uses = array('Sentence','Translation','Contribution','SentenceComment');
    
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
            'index','show','search', 'add_comment', 'random', 'go_to_sentence',
            'statistics', 'count_unknown_language', 'get_translations',
            'check_translation', 'change_language', 'several_random_sentences'
        );
    }

    /**
     * Redirects to a random sentence.
     * 
     * @return void
     */
    public function index()
    {
        $this->redirect('/sentences/show/random');
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

        Sanitize::html($id);

        if ($id == "random" OR $id == null OR $id == "" ) {
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
            $contributions = $this->Contribution->getContributionsRelatedToSentence(
                $id
            );
            $comments = $this->SentenceComment->getCommentsForSentence($id);
            $alltranslations = $this->Sentence->getTranslationsOf($id);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];
            
            if ($sentence != null) {
                $this->set('sentenceExists', true);
                $this->set('translations', $translations);
                $this->set('sentence', $sentence);
                $this->set('indirectTranslations', $indirectTranslations);
                $this->set('sentenceComments', $comments);
                $this->set('contributions', $contributions); 
            } else {
                $this->set('sentenceExists', false);
            }
            // checking which options user can access to
            $specialOptions = $this->Permissions->getSentencesOptions(
                $sentence, $this->Auth->user('id')
            );
            $this->set('specialOptions', $specialOptions);
            
        } else {
            // ----- other case -----
            $max = $this->Sentence->getMaxId();
            $randId = rand(1, $max);
            $this->Session->write('random_lang_selected', 'any');
            $this->redirect(array("action"=>"show", $randId ));
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

        if (rtrim($this->data['Sentence']['text']) != '' && !empty($userId)) {
            // setting correctness of sentence
            if ($this->Auth->user('group_id')) {
                $this->data['Sentence']['correctness'] 
                    = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
            } else {
                $this->data['Sentence']['correctness'] = 1;
            }
            
            // detecting language
            $this->GoogleLanguageApi->text = $this->data['Sentence']['text'];
            $response = $this->GoogleLanguageApi->detectLang();
            $this->data['Sentence']['lang'] 
                = $this->GoogleLanguageApi->google2TatoebaCode(
                    $response['language']
                );
            
            $this->data['Sentence']['user_id'] = $userId;
            
            // saving
            if ($this->Sentence->save($this->data)) {
                $sentence = $this->Sentence->getSentenceWithId($this->Sentence->id);
                $this->set('sentence', $sentence);
                
                $specialOptions = $this->Permissions->getSentencesOptions(
                    $sentence, $userId
                );
                $this->set('specialOptions', $specialOptions);
            }
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
        Sanitize::paranoid($id);
        $this->Sentence->delete($id, $this->Auth->user('id'));
        $this->flash(
            'The sentence #'.$id.' has been deleted.', '/sentences/show/'.$id
        );
    }
    
    /**
     * Save sentence.
     * Used in AJAX request, in sentences.contribute.js and in 
     * sentences.edit_in_place.js.
     *
     * @return void
     */
    public function save_sentence()
    {
        $userId = $this->Auth->user('id');

        if (isset($_POST['value']) AND rtrim($_POST['value'] != '')) {
            Sanitize::html($_POST['value']);
            
            if (isset($_POST['id'])) {
                // ---- sentences.edit_in_place.js -----
                
                Sanitize::paranoid($_POST['id']);
                
                // TODO HACK SPOTTED $_POST['id'] store 2 informations, lang and id
                // related to HACK in edit in place.js
                if (preg_match("/[a-z]/", $_POST['id'])) {
                    $hack_array = explode("_", $_POST["id"]);
                    $this->Sentence->id = $hack_array[1];
                    $this->data['Sentence']['lang'] = $hack_array[0]; 
                    // language is needed for the logs
                } else {
                    $this->Sentence->id = $_POST['id'];
                    $this->data['Sentence']['lang'] = null;
                    // language is needed for the logs
                }
                $this->data['Sentence']['text'] = rtrim($_POST['value']);
                $this->data['Sentence']['user_id'] = $userId;
                // user id is needed for the logs
                
                if ($this->Sentence->save($this->data)) {
                    $this->layout = null;
                    $this->set('sentence_text', rtrim($_POST['value']));
                }
                
                
            
            } else {
                // ----- sentences.contribute.js -----
                
                // setting correctness of sentence (which is not in use by the way)
                $this->data['Sentence']['correctness'] = 1;
                
                if ($this->Auth->user('group_id')) {
                    $this->data['Sentence']['correctness'] 
                        = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
                }
                
                // detecting language
                $this->GoogleLanguageApi->text = $_POST['value'];
                $response = $this->GoogleLanguageApi->detectLang();
                $this->data['Sentence']['lang'] 
                    = $this->GoogleLanguageApi->google2TatoebaCode(
                        $response['language']
                    );
                
                $this->data['Sentence']['user_id'] = $userId;
                $this->data['Sentence']['text'] = $_POST['value'];
                
                // saving
                if ($this->Sentence->save($this->data)) {
                    $this->layout = null;
                    
                    $sentenceId = $this->Sentence->id;
                    $sentence = $this->Sentence->getSentenceWithId($sentenceId);
                    
                    $specialOptions = $this->Permissions->getSentencesOptions(
                        $sentence,
                        $userId
                    );

                    $this->set('specialOptions', $specialOptions);
                    $this->set('sentence', $sentence);
                    $this->set('langResponse', $response);
                }
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
        Sanitize::paranoid($id);
        $data['Sentence']['id'] = $id;
        $data['Sentence']['user_id'] = $this->Auth->user('id');
        if ($this->Sentence->save($data)) {
            $this->flash(
                __(
                    'You are now the owner of this sentence and can modify it as '.
                    'you wish. It is your responsibility to make sure that it '.
                    ' doesn\'t have any mistake and, if possible, is not linked to '.
                    'wrong translations.', true
                ),
                '/sentences/show/'.$id
            );
        }
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
        Sanitize::paranoid($id);
        $data['Sentence']['id'] = $id;
        $data['Sentence']['user_id'] = null;
        if ($this->Sentence->save($data)) {
            $this->flash(
                __(
                    'You have abandoned your ownership for this sentence. '.
                    'Other people can now adopt it. If it was a mistake, you '.
                    'can just re-adopt it.', true
                ),
                '/sentences/show/'.$id
            );
        }
    }
    
    /**
     * Save the translation.
     *
     * @return void
     */ 
    public function save_translation()
    {

        Sanitize::html($_POST['value']);
        $userId = $this->Auth->user('id');
        
        if (isset($_POST['value']) AND rtrim($_POST['value']) != '' 
            AND isset($_POST['id']) AND !(empty($userId))
        ) {
            // Id of original sentence
            $sentenceId = $_POST['id'];
            
            // So that it saves a new sentences, otherwise it's like editing :
            $this->data['Sentence']['id'] = null; 
            
            // Language of original sentence, needed for the logs
            $this->data['Sentence']['sentence_lang'] = $_POST['lang']; 
            
            // If we want the "HasAndBelongsToMany" association to work, 
            // we need the two lines below : 
            $this->Sentence->id = $sentenceId;
            $this->data['Translation']['Translation'][] = $sentenceId;
            $this->data['InverseTranslation']['InverseTranslation'][] = $sentenceId;
            
            // Detecting language of translation
            $this->GoogleLanguageApi->text = $_POST['value'];
            $response = $this->GoogleLanguageApi->detectLang();
            $this->data['Sentence']['lang'] 
                = $this->GoogleLanguageApi->google2TatoebaCode(
                    $response['language']
                );
            
            // Sentence text
            $this->data['Sentence']['text'] = $_POST['value'];
            
            // User who added the translation
            $this->data['Sentence']['user_id'] = $userId;
            
            // Saving...
            if ($this->Sentence->save($this->data)) {
                $this->set('translation_id', $this->Sentence->id);
                $this->set('translation_lang', $this->data['Sentence']['lang']);
                $this->set('translation_text', $_POST['value']);
            }
        }
    }
    
    /**
     * Search sentences.
     *
     * @return void
     */
    public function search()
    {
        
        if (isset($_GET['query'])) {
            $query = $_GET['query'];    
            Sanitize::html($query);

            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                Sanitize::html($page);
            } else {
                $page = null;
            }
            
            if (isset($_GET['from']) && $_GET['from'] != 'und') {
                $from = $_GET['from'];
            } else {
                $from = null;
            }
            
            if (isset($_GET['to']) && $_GET['to'] != 'und') {
                $to = $_GET['to'];
            } else {
                $to = null;
            }            
            
            
            $this->Session->write('search_query', $query);
            $this->Session->write('search_from', $from);
            $this->Session->write('search_to', $to);
            
            $lucene_results = $this->Lucene->search($query, $from, $to, $page);
            $sentences = array();
            
            $ids = array();
            $scores = array();
            
            if ( isset($lucene_results['sentencesIds'])) {
                foreach ($lucene_results['sentencesIds'] as $result) {
                    $ids[] = $result['id'];
                    $scores[] = $result['score'];
                }
            }
            
            $sentences = $this->Sentence->getSentencesWithIds($ids, $to);
            
            $resultsInfo['currentPage'] = $lucene_results['currentPage'];
            $resultsInfo['pagesCount'] = $lucene_results['pagesCount'];
            $resultsInfo['sentencesPerPage'] = $lucene_results['sentencesPerPage'];
            $resultsInfo['sentencesCount'] = $lucene_results['sentencesCount'];
            
            $mostFrequentWords = $lucene_results['mostFrequentWords'];
            
            $this->set('results', $sentences);
            $this->set('resultsInfo', $resultsInfo);
            $this->set('mostFrequentWords', $mostFrequentWords);
            $this->set('scores', $scores);
            $this->set('query', $query);
            $this->set('from', $from);
            $this->set('to', $to);
            
            
            // checking which options user can access to
            $specialOptions = array();
            foreach ($sentences as $sentence) {
                $specialOptions[] = $this->Permissions->getSentencesOptions(
                    $sentence, $this->Auth->user('id')
                );
            }
            $this->set('specialOptions', $specialOptions);
        } else {
            //TODO pageTitle should be done in the view
            $this->pageTitle = __('Tatoeba search', true);
            $this->redirect(
                array(
                    "lang" => $this->params['lang'], 
                    "controller" => "pages", 
                    "action" => "display", 
                    "search"
                )
            );
        }
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
            $lang = $this->Session->read('random_lang_selected');
        }
        
        $randomId = $this->Sentence->getRandomId($lang);
        $randomSentence = $this->Sentence->getSentenceWithId($randomId);
        $alltranslations = $this->Sentence->getTranslationsOf($randomId);
        $translations = $alltranslations['Translation'];
        $indirectTranslations = $alltranslations['IndirectTranslation'];
       
        $this->Session->write('random_lang_selected', $lang);
        $randomSentence['specialOptions'] = $this->Permissions->getSentencesOptions(
            $randomSentence, $this->Auth->user('id')
        );
        
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
        } else {
            // default number of sentences when coming from "show more..."
            $number = 5; 
        }
        
        if (isset($_POST['data']['Sentence']['into'])) {
            $lang = $_POST['data']['Sentence']['into'] ;
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
        $allSentences = array();

        $randomIds = $this->Sentence->getSeveralRandomIds($lang, $number);

        foreach ($randomIds as $i=>$randomId ) {

            $randomSentence = $this->Sentence->getSentenceWithId($randomId);
            
            $this->Session->write('random_lang_selected', $lang);
            $randomSentence['specialOptions'] 
                = $this->Permissions->getSentencesOptions(
                    $randomSentence, $this->Auth->user('id')
                );

            $alltranslations = $this->Sentence->getTranslationsOf($randomId);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            $allSentences[$i] = array (
                "Sentence" => $randomSentence,
                "Translations" => $translations,
                "IndirectTranslations" => $indirectTranslations
            );

        }

        $this->set("allSentences", $allSentences);
        $this->set('lastNumberChosen', $number);
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
     * Display current user's sentences.
     *
     * @return void
     */
    public function my_sentences()
    {
        $sentences = $this->paginate(
            'Sentence', array('Sentence.user_id' => $this->Auth->user('id'))
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
        if (isset($_POST['id']) AND isset($_POST['lang'])) {
            $this->Sentence->id = $_POST['id'];
            $this->Sentence->saveField('lang', $_POST['lang']);
        }
    }
}
?>
