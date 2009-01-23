<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html', 'Logs', 'Pagination', 'Comments');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','search', 'add_comment', 'random', 'goTo');
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
			
			//$this->Sentence->recursive = -1;
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
			
			$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			$this->data['Sentence']['user_id'] = $this->Auth->user('id');
			// saving
			if($this->Sentence->save($this->data)){
				// Logs
				$this->data['Contribution']['sentence_id'] = $this->Sentence->id;
				$this->data['Contribution']['sentence_lang'] = $response['language'];
				$this->data['Contribution']['text'] = $this->data['Sentence']['text'];
				$this->data['Contribution']['action'] = 'insert';
				$this->data['Contribution']['user_id'] = $this->Auth->user('id');
				$this->data['Contribution']['datetime'] = date("Y-m-d H:i:s");
				$this->data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Sentence->Contribution->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('Your sentence has been saved. You can add a translation for it or add another new sentence.',true), 
					'/sentences/contribute/'.$this->Sentence->id
				);
			}
		}
	}
	
	function delete($id){
		// We log first
		$this->Sentence->id = $id;
		$tmp = $this->Sentence->read();
		$this->data['Contribution']['sentence_id'] = $id;
		$this->data['Contribution']['sentence_lang'] = $tmp['Sentence']['lang'];
		$this->data['Contribution']['text'] = $tmp['Sentence']['text'];
		$this->data['Contribution']['action'] = 'delete';
		$this->data['Contribution']['user_id'] = $this->Auth->user('id');
		$this->data['Contribution']['datetime'] = date("Y-m-d H:i:s");
		$this->data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
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
			if($this->Sentence->save($this->data)){				
				// Sentence logs
				$this->data['Contribution']['sentence_id'] = $this->Sentence->id;
				$this->data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
				$this->data['Contribution']['text'] = $this->data['Sentence']['text'];
				$this->data['Contribution']['action'] = 'update';
				$this->data['Contribution']['user_id'] = $this->Auth->user('id');
				$this->data['Contribution']['datetime'] = date("Y-m-d H:i:s");
				$this->data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Sentence->Contribution->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('The sentence has been updated.',true),
					'/sentences/edit/'.$id
				);
			}
		}
	}
	
	function adopt($id){
		$data['Sentence']['id'] = $id;
		$data['Sentence']['user_id'] = $this->Auth->user('id');
		if($this->Sentence->save($data)){
			$this->flash(
				__('You are now owner of this sentence. Besides of moderators and administratos, ONLY YOU can modify it (notice the "Edit" option next to "Translate"). It is now your responsibility to make sure that this does not have any mistake and is not linked to wrong translations.',true),
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
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences', false); 
				// I'm actually not sure if the "false" does something here... but oh well.
				// see : http://micropipes.com/blog/2008/01/07/cakephps-cache-that-wouldnt-quit/
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->redirect(array("action"=>"translate", $randId));
			//$this->Sentence->id = $randId;
		}else{
			$this->Sentence->id = $id;
			$sentence = $this->Sentence->read();
			$this->set('sentence',$sentence);
			$this->data['Sentence']['id'] = $id;
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions($sentence['Sentence']['user_id'], $this->Auth->user('id'));
			$this->set('specialOptions',$specialOptions);
		}
	}
	
	function save_translation(){
		if(!empty($this->data)){
			// If we want the "HasAndBelongsToMany" association to work, we need the two lines below :
			$this->Sentence->id = $this->data['Sentence']['id'];
			$this->data['Translation']['Translation'][] = $this->data['Sentence']['id'];
			
			// And this is because the translations are reciprocal :
			$this->data['InverseTranslation']['InverseTranslation'][] = $this->data['Sentence']['id'];
			
			$this->data['Sentence']['id'] = null; // so that it saves a new sentences, otherwise it's like editing
			
			// setting level of correctness
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
			
			if($this->Sentence->save($this->data)){
				// Sentence logs
				$this->data['Contribution']['sentence_id'] = $this->Sentence->id;
				$this->data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
				$this->data['Contribution']['text'] = $this->data['Sentence']['text'];
				$this->data['Contribution']['action'] = 'insert';
				$this->data['Contribution']['user_id'] = $this->Auth->user('id');
				$this->data['Contribution']['datetime'] = date("Y-m-d H:i:s");
				$this->data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Sentence->Contribution->save($this->data);
				
				// Translation logs
				$data['Contribution']['sentence_id'] = $this->data['Translation']['Translation'][0];
				$data['Contribution']['sentence_lang'] = $this->data['Sentence']['sentence_lang'];
				$data['Contribution']['translation_id'] = $this->Sentence->id;
				$data['Contribution']['translation_lang'] = $this->data['Sentence']['lang'];
				$data['Contribution']['action'] = 'insert';
				$data['Contribution']['user_id'] = $this->Auth->user('id');
				$data['Contribution']['datetime'] = date("Y-m-d H:i:s");
				$data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
				$Contributions[] = $data;
				
				// Inverse translation logs
				$data['Contribution']['sentence_id'] = $this->Sentence->id;
				$data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
				$data['Contribution']['translation_id'] = $this->data['Translation']['Translation'][0];
				$data['Contribution']['translation_lang'] = $this->data['Sentence']['sentence_lang'];
				$data['Contribution']['action'] = 'insert';
				$data['Contribution']['user_id'] = $this->Auth->user('id');
				$data['Contribution']['datetime'] = date("Y-m-d H:i:s");
				$data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
				$Contributions[] = $data;
				
				$this->Sentence->Contribution->saveAll($Contributions);
				
				// Confirmation message
				$this->flash(
					__('Your translation has been saved.',true),
					'/sentences/translate/'.$this->data['Translation']['Translation'][0]
				);
			}else{
				$this->flash(
					__('A problem occured. Your translation has not been saved.',true),
					'/sentences/contribute/'.$this->data['Translation']['Translation'][0]
				);
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
			$sentences = $this->Sentence->find(
				'all', array("conditions" => array("Sentence.id" => $ids))
			);
			
			if($sentences != array()){
				$resultsInfo['currentPage'] = $lucene_results['currentPage'];
				$resultsInfo['pagesCount'] = $lucene_results['pagesCount'];
				$resultsInfo['sentencesPerPage'] = $lucene_results['sentencesPerPage'];
				$resultsInfo['sentencesCount'] = $lucene_results['sentencesCount'];
				
				$this->set('results', $sentences);
				$this->set('resultsInfo', $resultsInfo);
			}
			
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
	
	function random(){
		$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences', false);
		$max = $resultMax[0][0]['MAX(id)'];
		$randId = rand(1, $max);
		$this->Sentence->id = $randId;
		
		$this->Sentence->unbindModel(
			array(
				'hasMany' => array('SentenceComment', 'Contribution'),
				'hasAndBelongsToMany' => array('InverseTranslation')
			)
		);
		$random = $this->Sentence->read();
		$random['specialOptions'] = $this->Permissions->getSentencesOptions($random['Sentence']['user_id'], $this->Auth->user('id'));
		
		return $random;
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
}
?>