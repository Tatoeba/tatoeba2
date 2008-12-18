<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','add','translate','save_translation','search', 'add_comment', 'random', 'goTo');
	}

	
	function index(){
		$this->set('sentences',$this->Sentence->find('all'));
	}
	
	function show($id = null){
		if($id == "random"){
			$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences');
			$max = $resultMax[0][0]['MAX(id)'];
			
			$randId = rand(1, $max);
			$this->Sentence->id = $randId;
		}else{
			$this->Sentence->id = $id;
		}
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions();
		$this->set('specialOptions',$specialOptions);
		
		$this->set('sentence',$this->Sentence->read());
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
			
			if($response['language']){
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
				// saving
				if($this->Sentence->save($this->data)){
					// Logs
					$this->data['SentenceLog']['sentence_id'] = $this->Sentence->id;
					$this->data['SentenceLog']['sentence_lang'] = $response['language'];
					$this->data['SentenceLog']['sentence_text'] = $this->data['Sentence']['text'];
					$this->data['SentenceLog']['action'] = 'insert';
					$this->data['SentenceLog']['user_id'] = $this->Auth->user('id');
					$this->data['SentenceLog']['datetime'] = date("Y-m-d H:i:s");
					$this->Sentence->SentenceLog->save($this->data);
					
					// Confirmation message
					$this->flash(
						__('Your sentence has been saved.',true), 
						'/sentences'
					);
				}
			}else{
				echo 'Problem with language detection';
			}
		}
	}
	
	function delete($id){
		// We log first
		$this->Sentence->id = $id;
		$tmp = $this->Sentence->read();
		$this->data['SentenceLog']['sentence_id'] = $id;
		$this->data['SentenceLog']['sentence_lang'] = $tmp['Sentence']['lang'];
		$this->data['SentenceLog']['sentence_text'] = $tmp['Sentence']['text'];
		$this->data['SentenceLog']['action'] = 'delete';
		$this->data['SentenceLog']['user_id'] = $this->Auth->user('id');
		$this->data['SentenceLog']['datetime'] = date("Y-m-d H:i:s");
		$this->Sentence->SentenceLog->save($this->data);
		
		// Then we delete
		$this->Sentence->del($id);
		$this->flash('The sentence #'.$id.' has been deleted.', '/sentences');
	}

	function edit($id){
		$this->Sentence->id = $id;
		if(empty($this->data)){
			$this->data = $this->Sentence->read();
		}else{
			if($this->Sentence->save($this->data)){				
				// Logs
				$this->data['SentenceLog']['sentence_id'] = $this->Sentence->id;
				$this->data['SentenceLog']['sentence_lang'] = $this->data['Sentence']['lang'];
				$this->data['SentenceLog']['sentence_text'] = $this->data['Sentence']['text'];
				$this->data['SentenceLog']['action'] = 'update';
				$this->data['SentenceLog']['user_id'] = $this->Auth->user('id');
				$this->data['SentenceLog']['datetime'] = date("Y-m-d H:i:s");
				$this->Sentence->SentenceLog->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('The sentence has been updated',true),
					'/sentences/edit/'.$id
				);
			}
		}
	}
	
	function translate($id){
		$this->Sentence->id = $id;
		$this->set('sentence',$this->Sentence->read());
		$this->data['Sentence']['id'] = $id;
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions();
		$this->set('specialOptions',$specialOptions);	
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
			$this->data['Sentence']['lang'] = $response['language'];
			
			if($this->Sentence->save($this->data)){
				// Logs
				$this->data['TranslationLog']['sentence_id'] = $this->data['Translation']['Translation'][0];
				$this->data['TranslationLog']['sentence_lang'] = $this->data['Sentence']['sentence_lang'];
				$this->data['TranslationLog']['translation_id'] = $this->Sentence->id;
				$this->data['TranslationLog']['translation_lang'] = $this->data['Sentence']['lang'];
				$this->data['TranslationLog']['translation_text'] = $this->data['Sentence']['text'];
				$this->data['TranslationLog']['action'] = 'insert';
				$this->data['TranslationLog']['user_id'] = $this->Auth->user('id');
				$this->data['TranslationLog']['datetime'] = date("Y-m-d H:i:s");
				$this->Sentence->TranslationLog->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('The translation has been saved',true),
					'/'.$this->params['lang'].'/sentences/show/'.$this->data['Translation']['Translation'][0]
				);
			}else{
				echo 'problem';
			}
		}
	}
	
	function search($query = null){
		if($query != null){
			// because cakePHP escapes the "+" and I don't want that...
			$unescapedQuery = preg_replace("#[^(.)]*/sentences/search/([.]*)#", "$2", $_SERVER['REQUEST_URI']);
			$unescapedQuery = urldecode($unescapedQuery);
			$this->Session->write("unescapedQuery", $unescapedQuery);
			
			$this->pageTitle = __('Tatoeba search : ',true) . $unescapedQuery;
			$lucene_results = $this->Lucene->search($unescapedQuery);
			$sentences = array();
			
			foreach($lucene_results as $result){
				$sentence = $this->Sentence->findById($result['id']);
				$sentence['Score'] = $result['score'];
				$sentences[] = $sentence;
			}
			
			/*
			print_r($sentences);
			
			// would give something like this :
			Array ( 
				[0] => Array ( 
					[Sentence] => Array ( 
						[id] => 157 
						[lang] => en 
						[text] => "I can't think with that noise", she said as she stared at the typewriter. [F] 
						[correctness] => 
						[user_id] => 
						[created] => 
						[modified] => 
					) 
					[SuggestedModification] => Array ( ) 
					[SentenceLog] => Array ( ) 
					[TranslationLog] => Array ( ) 
					[Translation] => Array ( ) 
					[InverseTranslation] => Array ( ) 
					[Score] => 1 
				) 
			)
			*/
			
			$this->set('query', $unescapedQuery);
			
			if($sentences != array()){
				$this->set('results', $sentences);
			}
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions();
			$this->set('specialOptions',$specialOptions);
		}else{
			if(isset($this->data['Sentence']['query'])){
				$this->redirect(array("action" => "search", $this->data['Sentence']['query']));
			}else{
				$this->pageTitle = __('Tatoeba search',true);
			}
		}
	}
	
	function random(){
		$resultMax = $this->Sentence->query('SELECT MAX(id) FROM sentences');
		$max = $resultMax[0][0]['MAX(id)'];
		$randId = rand(1, $max);
		$this->Sentence->id = $randId;
		
		$random = $this->Sentence->read();
		$random['specialOptions'] = $this->Permissions->getSentencesOptions();
		
		return $random;
	}
}
?>