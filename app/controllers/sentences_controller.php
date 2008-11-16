<?php
class SentencesController extends AppController{
	var $name = 'Sentences';
	
	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show');
	}

	
	function index(){
		$this->set('sentences',$this->Sentence->find('all'));
	}
	
	function show($id){
		$this->Sentence->id = $id;
		$this->set('sentence',$this->Sentence->read());
	}
	
	function add(){
		if(!empty($this->data)){
			if($this->Sentence->save($this->data)){
				// Logs
				$this->data['SentenceLogs']['sentence_id'] = $this->Sentence->id;
				$this->data['SentenceLogs']['sentence_lang'] = $this->data['Sentence']['lang'];
				$this->data['SentenceLogs']['sentence_text'] = $this->data['Sentence']['text'];
				$this->data['SentenceLogs']['action'] = 'insert';
				$this->data['SentenceLogs']['user_id'] = $this->Auth->user('id');
				$this->data['SentenceLogs']['datetime'] = date("Y-m-d H:i:s");
				$this->Sentence->SentenceLogs->save($this->data);
				
				// Confirmation message
				$this->flash(
					__('Your post has been saved.',true), 
					'/sentences'
				);
			}
		}
	}
	
	function delete($id){
		// We log first
		$this->Sentence->id = $id;
		$tmp = $this->Sentence->read();
		$this->data['SentenceLogs']['sentence_id'] = $id;
		$this->data['SentenceLogs']['sentence_lang'] = $tmp['Sentence']['lang'];
		$this->data['SentenceLogs']['sentence_text'] = $tmp['Sentence']['text'];
		$this->data['SentenceLogs']['action'] = 'delete';
		$this->data['SentenceLogs']['user_id'] = $this->Auth->user('id');
		$this->data['SentenceLogs']['datetime'] = date("Y-m-d H:i:s");
		$this->Sentence->SentenceLogs->save($this->data);
		
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
				$this->flash(
					__('The sentence has been updated',true),
					'/sentences/edit/'.$id
				);
			}
		}
	}
	
	function translate($id){
		$this->Sentence->id = $id;
		$this->data['Sentence']['id'] = $id;
		$this->set('sentence',$this->Sentence->read());
	}
	
	function save_translation(){
		if(!empty($this->data)){
			// If we want the "HasAndBelongsToMany" association to work, we need the two lines below :
			$this->Sentence->id = $this->data['Sentence']['id'];
			$this->data['Translation']['Translation'][] = $this->data['Sentence']['id'];
			
			// And this is because the translations are reciprocal :
			$this->data['InverseTranslation']['InverseTranslation'][] = $this->data['Sentence']['id'];
			
			$this->data['Sentence']['id'] = null; // so that it saves a new sentences, otherwise it's like editing
			
			if($this->Sentence->save($this->data)){
				$this->flash(
					__('The translation has been saved',true),
					'/sentences'
				);
			}else{
				echo 'problem';
			}
		}
	}
}
?>