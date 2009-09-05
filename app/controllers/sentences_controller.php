<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html', 'Logs', 'Pagination', 'Comments', 'Navigation', 'Languages', 'Javascript');
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','search', 'add_comment', 'random', 'goTo', 'statistics', 'count_unknown_language', 'get_translations' , 'check_translation', 'map');
	}

	
	function index(){
		$this->redirect('/sentences/show/random');
	}
	
	function show($id = null){
		$languages = array('ar', 'de', 'en', 'es', 'fi', 'fr', 'he', 'it', 'id', 'jp', 'ko', 'nl', 'pt', 'ru', 'vn', 'zh');
		$this->Sentence->recursive = 1;

		$this->Sentence->unbindModel(
			array(
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation', 'Translation')
			)
		);			
		

		
		if($id == "random" OR $id == null){
			$id = $this->Session->read('random_lang_selected');
		}
		
		if($id == 'any'){
			
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences');
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->Session->write('random_lang_selected', $id);
			$this->redirect(array("action"=>"show", $randId));
			
		}elseif(in_array($id, $languages)){
			
			$conditions['Sentence.lang'] = $id;
			$random = $this->Sentence->find(
				'first', 
				array(
					'conditions' => $conditions,
					'order' => 'RAND()'
				)
			);
			$this->Session->write('random_lang_selected', $id);
			$this->redirect(array("action"=>"show", $random['Sentence']['id']));
			
		}else{
		
			$this->Sentence->id = $id;
			
			$sentence = $this->Sentence->read();
			$this->set('sentence', $sentence);
			pr($sentence);
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
			$this->set('specialOptions',$specialOptions);
			
		}
	}
	
	function goTo(){
		$id = intval($this->params['url']['sentence_id']);
		if($id == 0){
			$id = 'random';
		}
		$this->redirect(array("action"=>"show", $id));
	}
	
	function add(){
		if(!empty($this->data)){
			// setting correctness of sentence
			if($this->Auth->user('group_id')){
				$this->data['Sentence']['correctness'] = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
			}else{
				$this->data['Sentence']['correctness'] = 1;
			}
			
			// detecting language
			$this->GoogleLanguageApi->text = $this->data['Sentence']['text'];
			$response = $this->GoogleLanguageApi->detectLang();
			if($response['isReliable']){
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			}else{
				$this->data['Sentence']['lang'] = null;
			}
			
			$this->data['Sentence']['user_id'] = $this->Auth->user('id');
			
			// saving
			if($this->Sentence->save($this->data)){
				$this->data = null;
				
				$this->set('sentence', $this->Sentence->read());
				
				// checking which options user can access to
				// we use again this->data because of the argument
				// needed by getSentencesOptions 

				$this->data['Sentence']['user_id'] = $this->Auth->user('id');
				$specialOptions = $this->Permissions->getSentencesOptions($this->data, $this->Auth->user('id'));
				$this->data = null;
				$this->set('specialOptions',$specialOptions);
			}
		}
	}
	
	function delete($id){
		$this->Sentence->id = $id;
		
		// for the logs
		$this->Sentence->recursive = 1;
		$this->Sentence->read();
		$this->Sentence->data['User']['id'] = $this->Auth->user('id'); 
		
		//$this->Sentence->del($id, true); 
		// TODO : Deleting with del does not delete the right entries in sentences_translations.
		// But I didn't figure out how to solve that =_=;
		// So I'm just going to do something not pretty but whatever, I'm tired!!!
		$this->Sentence->query('DELETE FROM sentences WHERE id='.$id);
		$this->Sentence->query('DELETE FROM sentences_translations WHERE sentence_id='.$id);
		$this->Sentence->query('DELETE FROM sentences_translations WHERE translation_id='.$id);
		
		// need to call afterDelete() manually for the logs
		$this->Sentence->afterDelete();
		
		$this->flash('The sentence #'.$id.' has been deleted.', '/contributions/show/'.$id);
	}

	function edit($id){
		$this->Sentence->id = $id;
		if(empty($this->data)){
			$sentence = $this->Sentence->read();
			if($this->Auth->user('group_id') < 3 OR 
			$this->Auth->user('id') == $sentence['Sentence']['user_id']){
				$this->Sentence->recursive = 2;
				$this->data = $this->Sentence->read();
				$this->set('sentence', $this->data);
				$specialOptions = $this->Permissions->getSentencesOptions($this->data, $this->Auth->user('id'));
				$this->set('specialOptions',$specialOptions);
			}else{
				$this->flash(
					__('This sentence was not added by you. You can only edit sentences you have added.',true),
					'/sentences/show/'.$id
				);
			}
		}else{
			$this->data['Sentence']['user_id'] = $this->Auth->user('id'); // for the logs
			if($this->Sentence->save($this->data)){
				// Confirmation message
				$this->flash(
					__('The sentence has been updated.',true),
					'/sentences/edit/'.$id
				);
			}
		}
	}
	
	function save_sentence(){
		if(isset($_POST['value'])){
			if(isset($_POST['id'])){
				if(preg_match("/[a-z]/", $_POST['id'])){
					$this->Sentence->id = substr($_POST['id'], 2);
					$this->data['Sentence']['lang'] = substr($_POST['id'], 0, 2); // language needed for the logs
				}else{
					$this->Sentence->id = $_POST['id'];
					$this->data['Sentence']['lang'] = null; // language needed for the logs
				}
				$this->data['Sentence']['text'] = rtrim($_POST['value']);
				$this->data['Sentence']['user_id'] = $this->Auth->user('id'); // for the logs
				
				if($this->Sentence->save($this->data)){
					Configure::write('debug',0);
					$this->layout = null;
					$this->set('sentence_text', rtrim($_POST['value']));
				}
			}else{
				// setting correctness of sentence
				if($this->Auth->user('group_id')){
					$this->data['Sentence']['correctness'] = Sentence::MAX_CORRECTNESS - $this->Auth->user('group_id');
				}else{
					$this->data['Sentence']['correctness'] = 1;
				}
				
				// detecting language
				$this->GoogleLanguageApi->text = $this->data['Sentence']['text'];
				$response = $this->GoogleLanguageApi->detectLang();
				if($response['isReliable']){
					$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
				}else{
					$this->data['Sentence']['lang'] = null;
				}
				
				$this->data['Sentence']['user_id'] = $this->Auth->user('id');
				$this->data['Sentence']['text'] = $_POST['value'];
				
				// saving
				if($this->Sentence->save($this->data)){
					Configure::write('debug',0);
					$this->layout = null;
					$this->set('sentence', $this->Sentence->read());
					// checking which options user can access to
					// we use again this->data because of the argument needed by getSentencesOptions 
					$this->data['Sentence']['user_id'] = $this->Auth->user('id');
					$specialOptions = $this->Permissions->getSentencesOptions($this->data, $this->Auth->user('id'));
					$this->data = null;

					$this->set('specialOptions',$specialOptions);
				}
			}
		}
	}
	
	function adopt($id){
		$data['Sentence']['id'] = $id;
		$data['Sentence']['user_id'] = $this->Auth->user('id');
		if($this->Sentence->save($data)){
			$this->flash(
				__('You are now the owner of this sentence and can modify it as you wish. It is your responsibility to make sure that it doesn\'t have any mistake and, if possible, is not linked to wrong translations.',true),
				'/sentences/show/'.$id
			);
		}
	}
	
	function let_go($id){
		$data['Sentence']['id'] = $id;
		$data['Sentence']['user_id'] = null;
		if($this->Sentence->save($data)){
			$this->flash(
				__('You have abandonned your ownership for this sentence. Other people can now adopt it. If it was a mistake, you can just re-adopt it.',true),
				'/sentences/show/'.$id
			);
		}
	}

	
	// we always check the translation before we add it
	function check_translation(){
		if(isset($_POST['value']) AND rtrim($_POST['value']) != '' AND isset($_POST['id'])){
			$sentenceId = substr($_POST['id'], 2);
			$sourceLanguage = substr($_POST['id'], 0, 2); // language of the original sentence
			
			// detecting language of translation
			$this->GoogleLanguageApi->text = $_POST['value'];
			$response = $this->GoogleLanguageApi->detectLang();
			if($response['isReliable']){
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			}else{
				$this->data['Sentence']['lang'] = null;
			}
			
			// checking if same language...
			if ($sourceLanguage == $this->data['Sentence']['lang'] ) { 
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
	
	function save_translation(){
		if(isset($_POST['value']) AND rtrim($_POST['value']) != '' AND isset($_POST['id'])){
			$sentence_id = substr($_POST['id'], 2); // id of original sentence
			$this->data['Sentence']['sentence_lang'] = substr($_POST['id'], 0, 2); // language of original sentence, needed for the logs
			
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
				//Configure::write('debug',0);
				$this->set('translation_id', $this->Sentence->id);
				$this->set('translation_lang', $this->data['Sentence']['lang']);
				$this->set('translation_text', $_POST['value']);
			}
		}
	}
	
	function search(){
		if(isset($_GET['query'])){
			$query = stripslashes($_GET['query']);
			$page = isset($_GET['page']) ? $_GET['page'] : null;
			$from = isset($_GET['from']) ? $_GET['from'] : null;
			$to = isset($_GET['to'])   ? $_GET['to']   : null;
			$this->Session->write('search_query', $query);
			$this->Session->write('search_from', $from);
			$this->Session->write('search_to', $to);
			
			$this->pageTitle = __('Tatoeba search : ',true) . $query;
			$lucene_results = $this->Lucene->search($query, $from, $to, $page);
			$sentences = array();
			
			$ids = array();
			$scores = array();
			foreach($lucene_results['sentencesIds'] as $result){
				$ids[] = $result['id'];
				$scores[] = $result['score'];
			}
			
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
	
	function random($type = null, $lang = null){
		//Configure::write('debug',0);
		
		// $type can be "show" or "translate"
		// "translate" is used for the random sentence to translate in the "Contribution" section.
		// "show" is used anywhere else.
		if($type == 'translate'){
			$this->Sentence->recursive = 0;
		}
		
		$this->Sentence->unbindModel(
			array(
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
		
		if($lang == null){
			$lang = $this->Session->read('random_lang_selected');
		}
		
		if($lang == 'any'){
			
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences', false);
			$max = $resultMax[0][0]['MAX(id)'];
			$randId = rand(1, $max);
			
			$this->Sentence->id = $randId;
			$random = $this->Sentence->read();
			$this->Session->write('random_lang_selected',$lang);
			
			
		}elseif($lang == 'jp' OR $lang == 'en'){
		
			$min = ($lang == 'en') ? 15700 : 74000;
			$max = ($lang == 'en') ? 74000 : 127300;
			$randId = rand($min, $max);
			
			$random = $this->Sentence->find(
				'first', 
				array(
					'conditions' => array(
						'Sentence.id' => range($randId-50, $randId+50),
						'Sentence.lang' => $lang
					)
				)
			);
			$this->Session->write('random_lang_selected', $lang);
			
		}else{
		
			$conditions['Sentence.lang'] = $lang;
			$random = $this->Sentence->find(
				'first', 
				array(
					'conditions' => $conditions,
					'order' => 'RAND()'
				)
			);
			
			$this->Session->write('random_lang_selected', $lang);
			// TODO : find another solution than using RAND() because it's quite slow.
		}
		
		$random['specialOptions'] = $this->Permissions->getSentencesOptions($random, $this->Auth->user('id'));
		
		$this->set('random', $random);
		$this->set('type', $type);
	}
	
	function contribute($id = null){
		if(isset($id)){
			$this->Sentence->id = $id;
			$sentence = $this->Sentence->read();
			$sentence['specialOptions'] = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
		}else{
			$sentence = $this->random();	
		}
		
		$this->set('sentence', $sentence['Sentence']);
		$this->set('translations', $sentence['Translation']);
		$this->set('specialOptions', $sentence['specialOptions']);
	}
	
	function statistics(){
		$this->Sentence->recursive = -1;
		$stats = $this->Sentence->find('all', array(
				'fields' => array('Sentence.lang', 'count(*) as count'), 
				'order' => 'count DESC',
				'group' => 'Sentence.lang',
				'limit' => 5
			)
		);
		return($stats);
	}
	
	function link($id){
		$this->Sentence->unbindModel(
			array(
				'belongsTo' => array('User'),
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
		$this->Sentence->recursive = 2;
		$sentence = $this->Sentence->read();
		$specialOptions = $this->Permissions->getSentencesOptions($sentence, $this->Auth->user('id'));
		
		$this->set('sentence', $sentence);
		$this->set('specialOptions',$specialOptions);
	}
	
	function count_unknown_language(){
		$this->Sentence->recursive = -1;
		$count = $this->Sentence->find('count', array(
				"conditions" => array(
					  "Sentence.user_id" => $this->Auth->user('id')
					, "Sentence.lang" => null
				)
			)
		);
		return $count;
	}
	
	function unknown_language(){
		$this->Sentence->recursive = -1;
		$sentences = $this->Sentence->find('all', array(
				"conditions" => array(
					  "Sentence.user_id" => $this->Auth->user('id')
					, "Sentence.lang" => null
				)
			)
		);
		$this->set('unknownLangSentences', $sentences);
	}
	
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
		//pr($this->data);
	}
	
	function get_translations($id){
		Configure::write('debug',0);
		$this->Sentence->unbindModel(
			array(
				'belongsTo' => array('User'),
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
		$this->layout = null;
		$this->Sentence->id = $id;
		$this->Sentence->recursive = 2;
		$sentence = $this->Sentence->read();
		$this->set('sentence', $sentence);
	}
	
	function my_sentences(){
		$this->Sentence->recursive = 0;
		$sentences = $this->Sentence->find(
			'all', array(
				"conditions" => array("Sentence.user_id" => $this->Auth->user('id')),
				"order" => "Sentence.modified DESC")
		);
		$this->set('user_sentences', $sentences);
	}
	
	function map($page = 1){
		$total = 10000;
		$start = ($page-1) * $total;
		$end = $start + $total;
		$this->Sentence->recursive = -1;
		$sentences = $this->Sentence->find(
			'all',
			array(
				'fields' => array('Sentence.id', 'Sentence.lang'),
				'order' => 'Sentence.id',
				'conditions' => array('Sentence.id >' => $start, 'Sentence.id <=' => $end)
			)
		);
		$this->set('page', $page);
		$this->set('all_sentences', $sentences);
	}
}
?>
