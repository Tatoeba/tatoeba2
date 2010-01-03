<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

App::import('Core', 'Sanitize');

class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html', 'Logs', 'Pagination', 'Comments', 'Navigation', 'Languages', 'Javascript');
	var $paginate = array('limit' => 100, "order" => "Sentence.modified DESC");
    var $uses = array('Sentence','Translation','Contribution','SentenceComment');
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','search', 'add_comment', 'random', 'goToSentence', 'statistics', 'count_unknown_language',
        'get_translations' , 'check_translation', 'change_language', 'several_random_sentences');
	}

	
	function index(){
		$this->redirect('/sentences/show/random');
	}
	
	
	/**
	 * Show sentence of specified id (or a random one if no id specified).
	 */

    // TODO HACK SPOTTED : nested controller and model code
    // need to create a specific method in the model to get sentence from
    // database 
	function show($id = null){

        Sanitize::html($id);
		
		//$this->Sentence->hasMany['SentenceComment']['limit'] = 4; // limiting comments to 4, but we're actually only going to display 3.

		if($id == "random" OR $id == null OR $id == "" ){
			$id = $this->Session->read('random_lang_selected');
		}

        // if we want a random sentence in a specific language
		if(in_array($id, $this->Sentence->languages)){
			
            $random = $this->Sentence->getRandomId($id);
            
			$this->Session->write('random_lang_selected', $id);
			$this->redirect(array("action"=>"show", $random));
			
        // if we give directly an id
		}elseif (is_numeric($id)){
		
			$sentence = $this->Sentence->getShowSentenceWithId($id);
            $translations = $this->Sentence->getTranslationsOf($id);
            $contributions = $this->Contribution->getContributionsRelatedToSentence($id);
            $comments = $this->SentenceComment->getCommentsForSentence($id);

            $alltranslations = $this->Sentence->getTranslationsOf($id);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            //pr ($translations) ;
            //pr ($indirectTranslations);
            //pr ($sentence);
            //pr ($contributions);
            if($sentence != null){
    			$this->set('sentenceExists', true);
                $this->set('translations' ,$translations);
                $this->set('sentence', $sentence);
                $this->set('indirectTranslations', $indirectTranslations);
                $this->set('sentenceComments',$comments);
                $this->set('contributions',$contributions); 
            }else{
                $this->set('sentenceExists', false);
            }
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
			$this->set('specialOptions',$specialOptions);
			

        // other case
		}else {
			$max = $this->Sentence->getMaxId();
			$randId = rand(1, $max);
			$this->Session->write('random_lang_selected', 'any');
			$this->redirect(array("action"=>"show", $randId ));


		}
	}
	
	
	/**
	 * Display sentence of specified id.
	 */
	function goToSentence(){
		$id = intval($this->params['url']['sentence_id']);
		if($id == 0){
			$id = 'random';
		}
		$this->redirect(array("action"=>"show", $id));
	}
	
	
	/**
	 * Add a new sentence.
	 */
	function add(){
		$id_temp = $this->Auth->user('id');

		if((!empty($this->data)) && (!empty($id_temp)) ){
			// setting correctness of sentence
			if($this->Auth->user('group_id')){
				$this->data['Sentence']['correctness'] = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
			}else{
				$this->data['Sentence']['correctness'] = 1;
			}
			
			// detecting language
			$this->GoogleLanguageApi->text = $this->data['Sentence']['text'];
			$response = $this->GoogleLanguageApi->detectLang();
			$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			
			$this->data['Sentence']['user_id'] = $this->Auth->user('id');
			
			// saving
			if($this->Sentence->save($this->data)){
				$sentence = $this->Sentence->read();
				$this->set('sentence', $sentence);
				
				$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
				$this->set('specialOptions',$specialOptions);
			}
		}
	}
	
	
	/**
	 * Delete a sentence.
	 */
	function delete($id){
        Sanitize::paranoid($id);
		$this->Sentence->delete($id)
;
		$this->flash('The sentence #'.$id.' has been deleted.', '/contributions/show/'.$id);
	}
	
	
	/**
	 * Save sentence.
	 * Used in AJAX request, in sentences.contribute.js and in sentences.edit_in_place.js.
	 */
	function save_sentence(){
		if(isset($_POST['value'])){
			Sanitize::html($_POST['value']);
			// sentences.edit_in_place.js
			if(isset($_POST['id'])){
                Sanitize::paranoid($_POST['id']);
				

                // TODO HACK SPOTTED $_POST['id'] store 2 informations, lang and id
                // related to HACK in edit in place.js
				if(preg_match("/[a-z]/", $_POST['id'])){
                    $hack_array = explode( "_" , $_POST["id"]);
					$this->Sentence->id = $hack_array[1];
					$this->data['Sentence']['lang'] = $hack_array[0]; // language needed for the logs
				}else{
					$this->Sentence->id = $_POST['id'];
					$this->data['Sentence']['lang'] = null; // language needed for the logs
				}
				$this->data['Sentence']['text'] = rtrim($_POST['value']);
				$this->data['Sentence']['user_id'] = $this->Auth->user('id'); // for the logs
				
				if($this->Sentence->save($this->data)){
					$this->layout = null;
					$this->set('sentence_text', rtrim($_POST['value']));
				}
			}
			
			// sentences.contribute.js
			else{
				// setting correctness of sentence (which is not in use by the way)
				if($this->Auth->user('group_id')){
					$this->data['Sentence']['correctness'] = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
				}else{
					$this->data['Sentence']['correctness'] = 1;
				}
				
				// detecting language
				$this->GoogleLanguageApi->text = $_POST['value'];
				$response = $this->GoogleLanguageApi->detectLang();
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
				
				
				$this->data['Sentence']['user_id'] = $this->Auth->user('id');
				$this->data['Sentence']['text'] = $_POST['value'];
				
				// saving
				if($this->Sentence->save($this->data)){
					$this->layout = null;
					
					$sentence = $this->Sentence->read();
					$this->set('sentence', $sentence);
					
					$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
					$this->set('specialOptions',$specialOptions);
					
					$this->set('langResponse', $response);
				}
			}
		}
	}
	
	
	/**
	 * Adopt a sentence. User can modify sentence and becomes
	 * responsible of the sentence.
	 */
	function adopt($id){
        Sanitize::paranoid($id);
		$data['Sentence']['id'] = $id;
		$data['Sentence']['user_id'] = $this->Auth->user('id');
		if($this->Sentence->save($data)){
			$this->flash(
				__('You are now the owner of this sentence and can modify it as you wish. It is your responsibility to make sure that it doesn\'t have any mistake and, if possible, is not linked to wrong translations.',true),
				'/sentences/show/'.$id
			);
		}
	}
	
	
	/**
	 * Let go a sentence. Sentence does not belong to user anymore,
	 * i.e. user cannot modify it anymore, and is not responsible
	 * of it either.
	 */
	function let_go($id){

        Sanitize::paranoid($id);
		$data['Sentence']['id'] = $id;
		$data['Sentence']['user_id'] = null;
		if($this->Sentence->save($data)){
			$this->flash(
				__('You have abandoned your ownership for this sentence. Other people can now adopt it. If it was a mistake, you can just re-adopt it.',true),
				'/sentences/show/'.$id
			);
		}
	}

	
	/**
	 * Check if translation is same language as original sentence.
	 * We always check the translation before we save it.
	 * Used in AJAX request in sentences.add_translation.js.
	 */
	function check_translation(){
        Sanitize::html($_POST['value']);
		$id_temp = $this->Auth->user('id');
		if( isset($_POST['value'])
            AND rtrim($_POST['value']) != '' 
            AND isset($_POST['id'])
            AND !(empty($id_temp))) {

			$sentenceId = $_POST['id'];
			$sourceLanguage = $_POST['lang']; // language of the original sentence
			
			// detecting language of translation
			$this->GoogleLanguageApi->text = $_POST['value'];
			$response = $this->GoogleLanguageApi->detectLang();
			$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			
			
			// checking if same language...
			if ($sourceLanguage == $this->data['Sentence']['lang'] AND !empty($this->data['Sentence']['lang']) ) { 
				// it will display a warning
				$this->set('sentence_id', $sentenceId);
				$this->set('translation_text', $_POST['value']);
			}else{
				// we save
				$this->data['Sentence']['differentLang'] = true; // so we know the sentence is not same language as original
				$this->save_translation();
				// note : for some reason it won't redirect to save_translation.ctp...
				// so we have to do everything in the check_translation.ctp
			}		
		}
	}
	
	/**
	 * Save the translation.
	 */ 
	function save_translation(){

        Sanitize::html($_POST['value']);
		$id_temp = $this->Auth->user('id');
		if(isset($_POST['value']) AND rtrim($_POST['value']) != '' AND isset($_POST['id']) AND !(empty($id_temp))){
			$sentence_id = $_POST['id']; // id of original sentence
			$this->data['Sentence']['sentence_lang'] = $_POST['lang']; // language of original sentence, needed for the logs
			
			// If we want the "HasAndBelongsToMany" association to work, we need the two lines below :			
			$this->Sentence->id = $sentence_id;
			$this->data['Translation']['Translation'][] = $sentence_id;
			
			// And this is because the translations are reciprocal :
			$this->data['InverseTranslation']['InverseTranslation'][] = $sentence_id;
			
			$this->data['Sentence']['id'] = null; // so that it saves a new sentences, otherwise it's like editing
			
			// setting level of correctness
			if($this->Auth->user('group_id')){
				$this->data['Sentence']['correctness'] = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
			}else{
				$this->data['Sentence']['correctness'] = 1;
			}
			
			// In the case where the user had to confirm saving, $this->data['Sentence']['differentLang'] is not set 
			// and the language of the translation is the same as the language of the sentence.
			if(!isset($this->data['Sentence']['differentLang'])){
				$this->data['Sentence']['lang'] = $this->data['Sentence']['sentence_lang'];
			}
			
			$this->data['Sentence']['text'] = $_POST['value'];
			$this->data['Sentence']['user_id'] = $this->Auth->user('id');		 	
			
			if($this->Sentence->save($this->data)){
				$this->set('translation_id', $this->Sentence->id);
				$this->set('translation_lang', $this->data['Sentence']['lang']);
				$this->set('translation_text', $_POST['value']);
			}
		}
	}
	
	
	/**
	 * Search sentences.
	 */
	function search() {
        
		if(isset($_GET['query'])){
			$query = stripslashes($_GET['query']);
			
			if(isset($_GET['page'])){
				$page = $_GET['page'];
			}else{
				$page = null;
			}
			
			if(isset($_GET['from']) AND $_GET['from'] != 'und'){
				$from = $_GET['from'];
			}else{
				$from = null;
			}
			
			if(isset($_GET['to']) AND $_GET['to'] != 'und'){
				$to = $_GET['to'];
			}else{
				$to = null;
			}			
			
			Sanitize::html($query);
			Sanitize::html($page);
			
			$this->Session->write('search_query', $query);
			$this->Session->write('search_from', $from);
			$this->Session->write('search_to', $to);
			
			$lucene_results = $this->Lucene->search($query, $from, $to, $page);
			$sentences = array();
			
			$ids = array();
			$scores = array();

            if ( isset($lucene_results['sentencesIds'])){
                foreach($lucene_results['sentencesIds'] as $result){
                    $ids[] = $result['id'];
                    $scores[] = $result['score'];
                }
            }
		
            /* model */	
			$this->Sentence->unbindModel(
				array(
					'hasMany' => array('SentenceComment', 'Contribution'),
					'hasAndBelongsToMany' => array('InverseTranslation')
				)
			);
			if($to != null){
				$this->Sentence->hasAndBelongsToMany['Translation']['conditions'] = array("lang" => $to);
			}
			$sentences = $this->Sentence->find(
				'all', array("conditions" => array("Sentence.id" => $ids))
			);
            /*end of model */
			
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
			foreach($sentences as $sentence){
				$specialOptions[] = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
			}
			$this->set('specialOptions',$specialOptions);
		}else{
			$this->pageTitle = __('Tatoeba search',true);
			$this->redirect(array("lang" => $this->params['lang'], "controller" => "pages", "action" => "display", "search"));			
		}
	}
	
	
	/**
	 * Show random sentence.
     * TODO : hack spotted => is type used correctly ???
	 */
	function random($type = null, $lang = null){		
		// $type can be "show" or "translate"
		// "translate" is used for the random sentence to translate in the "Contribution" section.
		// "show" is used anywhere else.
			
			
		if($lang == null){
			$lang = $this->Session->read('random_lang_selected');
		}
		
            $randomId = $this->Sentence->getRandomId($lang,$type);
            $randomSentence = $this->Sentence->getSentenceWithId($randomId,$type);
            $alltranslations = $this->Sentence->getTranslationsOf($randomId);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];
       
		$this->Session->write('random_lang_selected', $lang);
		$randomSentence['specialOptions'] = $this->Permissions->getSentencesOptions($randomSentence, $this->Auth->user('id'));
		
		$this->set('random', $randomSentence);
        $this->set('translations',$translations);
        $this->set('indirectTranslations', $indirectTranslations);
		$this->set('type', 'show');
	}
	/*
    ** show several random sentences a time
    ** NOTE : TODO : this just a work around ! 
    */

    function several_random_sentences(){
		if(isset($_POST['data']['Sentence']['numberWanted'])){
			$number = $_POST['data']['Sentence']['numberWanted'];
		}else{
			// default number of sentences when coming from "show more..."
			$number = 5; 
		}
		
		if(isset($_POST['data']['Sentence']['into'])){
			$lang = $_POST['data']['Sentence']['into'] ;
			$this->Session->write('random_lang_selected', $lang);
		}else{
			// default language when coming from "show more..."
			$lang = $this->Session->read('random_lang_selected');
		}
		
        $type = null ;
        // to avoid "petit malin" 
        if ( $number > 15 or $number < 1){
            $number = 5 ;
        } 
		
       // for far better perfomance we must do it in one request, but hmmm no time for that
       // and as said above that's a work around
        $allSentences = array ( );
        $randomIds = $this->Sentence->getSeveralRandomIds($lang,$number);
        foreach ($randomIds as $i=>$randomId ){

            $randomSentence = $this->Sentence->getSentenceWithId($randomId,'translate');
            
            $this->Session->write('random_lang_selected', $lang);
            $randomSentence['specialOptions'] = $this->Permissions->getSentencesOptions($randomSentence, $this->Auth->user('id'));

            $alltranslations = $this->Sentence->getTranslationsOf($randomId);
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            $allSentences[$i] = array (
                "Sentence" => $randomSentence,
                "Translations" => $translations,
                "IndirectTranslations" => $indirectTranslations

            );

        }
        $this->set("allSentences",$allSentences) ;
        $this->set('lastNumberChosen', $number);
    }
    	
	/**
	 * Count number of sentences in each language.
     * TODO : should be move in the model
     * TODO : should used a specific table instead of doing count
	 */
	function statistics(){
		$this->Sentence->recursive = -1;
		$stats = $this->Sentence->find('all', array(
				'fields' => array('Sentence.lang', 'count(*) as count'), 
				'order' => 'count DESC',
				'group' => 'Sentence.lang'
			)
		);
		return($stats);
	}
	
	
	/**
	 * Link two sentences...
	 * NOTE : this is not used yet.
	 */
	function link($id){
        Sanitize::paranoid($id);

        /*model */
		$this->Sentence->unbindModel(
			array(
				'belongsTo' => array('User'),
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
        // TODO : use containable instead !
		$this->Sentence->recursive = 2;
		$sentence = $this->Sentence->read();
        /*end of model*/
		$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
		
		$this->set('sentence', $sentence);
		$this->set('specialOptions',$specialOptions);
	}
	
	
	/**
	 * Count number of sentences that belongs to the current user
	 * and have an unidentified language.
	 * Called in requestAction in pages/home.ctp.
	 */
	function count_unknown_language(){
		return $this->Sentence->numberOfUnknownLanguageForUser($this->Auth->user('id'));
	}
	
	
	/**
	 * Display sentences with unknown language to let user
	 * set the language.
	 */
	function unknown_language(){
		$sentences = $this->Sentence->sentencesWithUnknownLanguageForUser($this->Auth->user('id'));
		$this->set('unknownLangSentences', $sentences);
	}
	
	/**
	 * Save languages for unknown language page.
	 */
	function set_languages(){
		if(!empty($this->data)){
			if($this->Sentence->saveAll($this->data['Sentence'])){
				$flashMsg = __('The languages have been saved.', true);
			}else{
				$flashMsg = __('A problem occured while trying to save.', true);
			}
		}else{
			$flashMsg = __('There is nothing to save.', true);
		}
		$this->flash(
			$flashMsg,
			'/sentences/unknown_language/'
		);
	}
	
	
	/**
	 * Display current user's sentences.
	 */
	function my_sentences(){

		$this->Sentence->recursive = 0; // => ??
		$sentences = $this->paginate('Sentence', array('Sentence.user_id' => $this->Auth->user('id')));
		// $sentences = $this->Sentence->find(
			// 'all', array(
				// "conditions" => array("Sentence.user_id" => $this->Auth->user('id')),
				// "order" => "Sentence.modified DESC")
		// );
		$this->set('user_sentences', $sentences);
	}
	
	
	/**
	 * Display how the sentences are clustered according to their language.
	 */
	function map($page = 1){
		$total = 10000;
		$start = ($page-1) * $total;
		$end = $start + $total;
        /*model */
		$this->Sentence->recursive = -1;// TODO : use containable
		$sentences = $this->Sentence->find(
			'all',
			array(
				'fields' => array('Sentence.id', 'Sentence.lang'),
				'order' => 'Sentence.id',
				'conditions' => array('Sentence.id >' => $start, 'Sentence.id <=' => $end)
			)
		);
        /* end of model */
		$this->set('page', $page);
		$this->set('all_sentences', $sentences);
	}
	
	
	/**
	 * Change language of a sentence.
	 * Used in AJAX request in sentences.change_language.js.
	 * TODO restrict permissions for this action.
	 */
	function change_language(){
		if(isset($_POST['id']) AND isset($_POST['lang'])){
			$this->Sentence->id = $_POST['id'];
			$this->Sentence->saveField('lang', $_POST['lang']);
		}
	}
}
?>
