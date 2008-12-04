<?php
class SentenceCommentsController extends AppController {
	var $name = 'SentenceComments';
	
	var $helpers = array('Comments','Sentences');
	var $components = array ('GoogleLanguageApi');
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index', 'save');
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
		$sentence = new Sentence();
		$sentence->id = $sentence_id;		
		$sentence->recursive = 2;
		$this->set('sentence', $sentence->read());	
		
		// checking which options user can access to
		$specialOptions = array('canComment' => false, 'canEdit' => false, 'canDelete' => false);
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			$specialOptions['canEdit'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/edit');
			$specialOptions['canDelete'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/delete');
		}
		
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
							'/sentences/show/'.$this->data['SentenceComment']['sentence_id']
						);
				}
			}
		}
	}
}
?>