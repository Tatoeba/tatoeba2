<?php
class SentenceCommentsController extends AppController {
	var $name = 'SentenceComments';
	
	var $components = array ('GoogleLanguageApi');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('*');
	}
	
	function index(){
		$comments = $this->SentenceComment->find(
			"all",
			array( 
				"conditions" => array("SentenceComment.lang" => "en"),
				"limit"=> 10,
				"order" => "SentenceComment.datetime DESC"
			)
		);
		$this->set('comments', $comments);
	}
	
	function add($sentence_id){
		$this->data['SentenceComment']['sentence_id'] = $sentence_id;
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
							'/sentences/show/'.$this->data['SentenceComment']['sentence_id']
						);
				}
			}
		}
	}
}
?>