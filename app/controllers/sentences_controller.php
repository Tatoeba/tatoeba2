<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	var $components = array ('GoogleLanguageApi', 'Lucene', 'Permissions');
	var $helpers = array('Sentences', 'Html', 'Logs', 'Pagination', 'Comments');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','add','translate',
			'save_translation','search', 'add_comment', 'random', 'goTo', 'contribute');
	}

	
	function index(){
		$this->set('sentences',$this->Sentence->find('all'));
	}
	
	function show($id = null){
		$this->Sentence->recursive = 2;
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
					$this->data['SentenceLog']['ip'] = $_SERVER['REMOTE_ADDR'];
					$this->Sentence->SentenceLog->save($this->data);
					
					// Confirmation message
					$this->flash(
						__('Your sentence has been saved. You can add a translation for it or add another new sentence.',true), 
						'/sentences/contribute/'.$this->Sentence->id
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
		$this->data['SentenceLog']['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->Sentence->SentenceLog->save($this->data);
		
		// Then we delete
		$this->Sentence->del($id);
		$this->flash('The sentence #'.$id.' has been deleted.', '/sentences');
	}

	function edit($id){
		$this->Sentence->id = $id;
		if(empty($this->data)){
			$this->Sentence->recursive = 2;
			$this->data = $this->Sentence->read();
			$this->set('sentence', $this->Sentence->read());
			$specialOptions = $this->Permissions->getSentencesOptions();
			$this->set('specialOptions',$specialOptions);	
		}else{
			if($this->Sentence->save($this->data)){				
				// Logs
				$this->data['SentenceLog']['sentence_id'] = $this->Sentence->id;
				$this->data['SentenceLog']['sentence_lang'] = $this->data['Sentence']['lang'];
				$this->data['SentenceLog']['sentence_text'] = $this->data['Sentence']['text'];
				$this->data['SentenceLog']['action'] = 'update';
				$this->data['SentenceLog']['user_id'] = $this->Auth->user('id');
				$this->data['SentenceLog']['datetime'] = date("Y-m-d H:i:s");
				$this->data['SentenceLog']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Sentence->SentenceLog->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('The sentence has been updated.',true),
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
			
			if($response['isReliable']){
				$this->data['Sentence']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
			}else{
				$this->data['Sentence']['lang'] = null;
			}
			
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
				$this->data['TranslationLog']['ip'] = $_SERVER['REMOTE_ADDR'];
				$this->Sentence->TranslationLog->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('Your translation has been saved. You can add another translation or add a new sentence.',true),
					'/sentences/contribute/'.$this->data['Translation']['Translation'][0]
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
			$query = $_GET['query'];
			$page = isset($_GET['page']) ? $_GET['page'] : null;
			
			$this->pageTitle = __('Tatoeba search : ',true) . $query;
			$lucene_results = $this->Lucene->search($query, null, null, $page);
			$sentences = array();
			
			foreach($lucene_results['sentencesIds'] as $result){
				$sentence = $this->Sentence->findById($result['id']);
				$sentence['score'] = $result['score'];
				$sentences[] = $sentence;
			}
			
			$this->set('query', $query);
			
			if($sentences != array()){
				$resultsInfo['currentPage'] = $lucene_results['currentPage'];
				$resultsInfo['pagesCount'] = $lucene_results['pagesCount'];
				$resultsInfo['sentencesPerPage'] = $lucene_results['sentencesPerPage'];
				$resultsInfo['sentencesCount'] = $lucene_results['sentencesCount'];
				
				$this->set('results', $sentences);
				$this->set('resultsInfo', $resultsInfo);
			}
			
			// checking which options user can access to
			$specialOptions = $this->Permissions->getSentencesOptions();
			$this->set('specialOptions',$specialOptions);
		}else{
			$this->pageTitle = __('Tatoeba search',true);
			$this->redirect(array("lang" => $this->params['lang'], "controller" => "pages", "action" => "display", "search"));			
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
	
	function contribute($id = null){
		if(isset($id)){
			$this->Sentence->id = $id;
			$sentence = $this->Sentence->read();
			$sentence['specialOptions'] = $this->Permissions->getSentencesOptions();
		}else{
			$sentence = $this->random();	
		}
		
		$this->set('sentence', $sentence['Sentence']);
		$this->set('translations', $sentence['Translation']);
		$this->set('specialOptions', $sentence['specialOptions']);
	}
}
?>