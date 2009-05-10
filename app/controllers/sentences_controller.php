<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html', 'Logs', 'Pagination', 'Comments', 'Navigation', 'Languages', 'Javascript');
	
	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','search', 'add_comment', 'random', 'goTo', 'statistics', 'count_unknown_language', 'get_translations');
	}

	
	function index(){
		$this->set('sentences',$this->Sentence->find('all'));
	}
	
	function show($id = null){
		if($id == "random"){
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences');
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->redirect(array("action"=>"show", $randId));
			//$this->Sentence->id = $randId;
		}else{
			$this->Sentence->id = $id;
			
			$this->Sentence->recursive = 1;
			$sentence = $this->Sentence->read();
			$this->set('sentence', $sentence);
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
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
				$specialOptions = $this->Permissions->getSentencesOptions($this->Auth->user('id'), $this->Auth->user('id'));
				$this->set('specialOptions',$specialOptions);
			}
		}
	}
	
	function delete($id){
		// We log first
		$this->Sentence->id = $id;
		$this->Sentence->recursive = 0;
		$this->Sentence->read();
		$this->Sentence->Contribution->save($this->data);
		
		// Then we delete
		$this->Sentence->del($id);
		$this->flash('The sentence #'.$id.' has been deleted.', '/sentences');
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
				$specialOptions = $this->Permissions->getSentencesOptions($this->data['Sentence']['user_id'], $this->Auth->user('id'));
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
				$this->Sentence->id = substr($_POST['id'], 2);
				$this->data['Sentence']['lang'] = substr($_POST['id'], 0, 2);
				$this->data['Sentence']['text'] = $_POST['value'];
				$this->data['Sentence']['user_id'] = $this->Auth->user('id'); // for the logs
				if($this->Sentence->save($this->data)){
					Configure::write('debug',0);
					$this->set('sentence_text', $_POST['value']);
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
					$this->set('sentence', $this->Sentence->read());
					// checking which options user can access to
					$specialOptions = $this->Permissions->getSentencesOptions($this->Auth->user('id'), $this->Auth->user('id'));
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
				__('You are now the owner of this sentence and can modify it as you wish (click on the "Edit" link for that). It is your responsibility to make sure that it doesn\'t have any mistake and, if possible, is not linked to wrong translations.',true),
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
	
	function translate($id){
		if($id == "random"){
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences');
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->redirect(array("action"=>"translate", $randId));
			//$this->Sentence->id = $randId;
		}else{
			$this->Sentence->id = $id;
			$this->Sentence->unbindModel(
				array(
					'belongsTo' => array('User'),
					'hasMany' => array('SentenceComment', 'Contribution'),
					'hasAndBelongsToMany' => array('InverseTranslation')
				)
			);
			$this->Sentence->recursive = 0;
			$sentence = $this->Sentence->read();
			$this->set('sentence',$sentence);
			$this->data['Sentence']['id'] = $id;
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
			$this->set('specialOptions',$specialOptions);
		}
	}
	
	function save_translation(){
		if(isset($_POST['value']) AND rtrim($_POST['value']) != '' AND isset($_POST['id'])){
			$sentence_id = substr($_POST['id'], 2);
			$this->data['Sentence']['sentence_lang'] = substr($_POST['id'], 0, 2); // needed for the logs
			
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
			
			$this->data['Sentence']['text'] = $_POST['value'];
			
			// detecting language
			$this->GoogleLanguageApi->text = $_POST['value'];
			$response = $this->GoogleLanguageApi->detectLang();
			if($response['isReliable']){
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			}else{
				$this->data['Sentence']['lang'] = null;
			}
			
			$this->data['Sentence']['user_id'] = $this->Auth->user('id');
			$this->data['Sentence']['lang'] = 'en'; // comment this line in prod mode
			
			if($this->Sentence->save($this->data)){
				Configure::write('debug',0);
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
			$this->Session->write('search_query', $query);
			$this->Session->write('search_from', $from);
			
			$this->pageTitle = __('Tatoeba search : ',true) . $query;
			$lucene_results = $this->Lucene->search($query, $from, null, $page);
			$sentences = array();
			
			$ids = array();
			$scores = array();
			foreach($lucene_results['sentencesIds'] as $result){
				$ids[] = $result['id'];
				$scores[] = $result['score'];
			}
			
			$this->Sentence->unbindModel(
				array(
					'belongsTo' => array('User'),
					'hasMany' => array('SentenceComment', 'Contribution'),
					'hasAndBelongsToMany' => array('InverseTranslation')
				)
			);
			$sentences = $this->Sentence->find(
				'all', array("conditions" => array("Sentence.id" => $ids))
			);
			
			
			$resultsInfo['currentPage'] = $lucene_results['currentPage'];
			$resultsInfo['pagesCount'] = $lucene_results['pagesCount'];
			$resultsInfo['sentencesPerPage'] = $lucene_results['sentencesPerPage'];
			$resultsInfo['sentencesCount'] = $lucene_results['sentencesCount'];
				
			$this->set('results', $sentences);
			$this->set('resultsInfo', $resultsInfo);	
			$this->set('sentences', $sentences);
			$this->set('scores', $scores);
			$this->set('query', $query);
			$this->set('from', $from);
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions(0,1);
			$this->set('specialOptions',$specialOptions);
		}else{
			$this->pageTitle = __('Tatoeba search',true);
			$this->redirect(array("lang" => $this->params['lang'], "controller" => "pages", "action" => "display", "search"));			
		}
	}
	
	function random($type = null){
		Configure::write('debug',0);
		$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences', false);
		$max = $resultMax[0][0]['MAX(id)'];
		$randId = rand(1, $max);
		$this->Sentence->id = $randId;
		
		if($type == 'translate'){
			$this->Sentence->recursive = 0;
		}
		
		$this->Sentence->unbindModel(
			array(
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
		$random = $this->Sentence->read();
		$random['specialOptions'] = $this->Permissions->getSentencesOptions($random['Sentence']['user_id'], $this->Auth->user('id'));
		
		$this->set('random', $random);
		$this->set('type', $type);
	}
	
	function contribute($id = null){
		if(isset($id)){
			$this->Sentence->id = $id;
			$sentence = $this->Sentence->read();
			$sentence['specialOptions'] = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
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
		$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
		
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
}
?>