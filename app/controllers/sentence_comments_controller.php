<?php
class SentenceCommentsController extends AppController {
	var $name = 'SentenceComments';
	
	var $helpers = array('Comments','Sentences', 'Languages', 'Tooltip');
	var $components = array ('GoogleLanguageApi', 'Permissions');
	
	var $langs = array('en', 'fr', 'jp', 'es', 'de');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index', 'save', 'show','latest');
	}
	
	function index(){
		$sentenceComments = array();
		
		$this->SentenceComment->recursive = 2;
		
		foreach($this->langs as $lang){
			$sentenceComments[$lang] = $this->SentenceComment->find(
				"all",
				array( 
					"conditions" => array("SentenceComment.lang" => $lang),
					"limit"=> 10,
					"order" => "SentenceComment.datetime DESC"
				)
			);
		}
		
		$this->set('sentenceComments', $sentenceComments);
	}
	
	function add($sentence_id){
		$sentence = new Sentence();
		$sentence->id = $sentence_id;
		$sentence->recursive = 2;
		$this->set('sentence', $sentence->read());	
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions();
		$this->set('specialOptions',$specialOptions);
	}
	
	// I don't like how 'show' is exactly the same as 'add' in the controller...
	// It's just the view that is different...
	function show($sentence_id){
		$sentence = new Sentence();
		$sentence->id = $sentence_id;		
		$sentence->recursive = 2;
		$this->set('sentence', $sentence->read());	
		
		// checking which options user can access to
		$specialOptions = $this->Permissions->getSentencesOptions();
		$this->set('specialOptions',$specialOptions);
	}
	
	function save(){
		if(!empty($this->data['SentenceComment']['text'])){
			// detecting language
			$this->GoogleLanguageApi->text = $this->data['SentenceComment']['text'];
			$response = $this->GoogleLanguageApi->detectLang();
			
			$this->data['SentenceComment']['user_id'] = $this->Auth->user('id');
			$this->data['SentenceComment']['datetime'] = date("Y-m-d H:i:s");
			
			if($response['language']){
				$this->data['SentenceComment']['lang'] = $this->GoogleLanguageApi->google2TatoebaCode($response['language']);
				if($this->SentenceComment->save($this->data)){
					$this->flash(
							__('Your comment has been saved.',true), 
							'/sentence_comments/show/'.$this->data['SentenceComment']['sentence_id']
						);
				}
			}
		}
	}
	
	
	function latest() {
		return $this->SentenceComment->find('all', array('order' => 'SentenceComment.datetime DESC', 'limit' => 5));
	}

}
?>